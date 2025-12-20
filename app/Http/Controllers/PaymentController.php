<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Item;
use Carbon\Carbon;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Models\SiteManagement;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticleNotificationMailable;
use App\Models\EmailTemplate;

class PaymentController extends Controller
{
    /**
     * @access protected
     * @var ExpressCheckout $provider
     */
    protected $provider;

    /**
     * @access private
     * @var array $email_settings
     */
    private $email_settings;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Get the PayPal provider instance.
     *
     * @return ExpressCheckout
     */
    protected function getProvider()
    {
        if (!$this->provider) {
            $this->provider = new ExpressCheckout();
        }
        return $this->provider;
    }

    /**
     * @access public
     * @desc Get response and redirect to a thank you page
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        $response = [];
        if (session()->has('code')) {
            $response['code'] = session()->get('code');
            session()->forget('code');
        }
        if (session()->has('message')) {
            $response['message'] = session()->get('message');
            session()->forget('message');
        }
        $error_code = session()->get('code');
        Session::flash('payment_message', $response);
        return redirect()->to('/user/products/thankyou');
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getExpressCheckout(Request $request)
    {
        $recurring = ($request->get('mode') === 'recurring') ? true : false;
        $cart = $this->getCheckoutData($recurring);
        $payment_detail = array();
        try {
            $response = $this->getProvider()->setExpressCheckout($cart, $recurring);
            return redirect($response['paypal_link']);
        } catch (\Exception $e) {
            $invoice = $this->createInvoice($cart, 'Invalid', $payment_detail);
            session()->put(['code' => 'danger', 'message' => "Error processing PayPal payment for Order $invoice->id!"]);
        }
    }

    /**
     * @access public
     * @desc Process payment on PayPal.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getExpressCheckoutSuccess(Request $request)
    {
        $recurring = ($request->get('mode') === 'recurring') ? true : false;
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');
        $cart = $this->getCheckoutData($recurring);
        // Verify Express Checkout Token
        $response = $this->getProvider()->getExpressCheckoutDetails($token);
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            if ($recurring === true) {
                $response = $this->getProvider()->createMonthlySubscription($response['TOKEN'], 9.99, $cart['subscription_desc']);
                if (!empty($response['PROFILESTATUS']) && in_array($response['PROFILESTATUS'], ['ActiveProfile', 'PendingProfile'])) {
                    $status = 'Processed';
                } else {
                    $status = 'Invalid';
                }
            } else {
                // Perform transaction on PayPal
                $payment_status = $this->getProvider()->doExpressCheckoutPayment($cart, $token, $PayerID);
                $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
            }
            $payment_detail = array();
            $payment_detail['payer_name'] = $response['FIRSTNAME'] . " " . $response['LASTNAME'];
            $payment_detail['payer_email'] = $response['EMAIL'];
            $payment_detail['seller_email'] = $payment_status['PAYMENTINFO_0_SELLERPAYPALACCOUNTID'];
            $payment_detail['currency_code'] = $response['CURRENCYCODE'];
            $payment_detail['payer_status'] = $response['PAYERSTATUS'];
            $payment_detail['transaction_id'] = $payment_status['PAYMENTINFO_0_TRANSACTIONID'];
            $payment_detail['sales_tax'] = $response['TAXAMT'];
            $payment_detail['invoice_id'] = $response['INVNUM'];
            $payment_detail['shipping_amount'] = $response['SHIPPINGAMT'];
            $payment_detail['handling_amount'] = $response['HANDLINGAMT'];
            $payment_detail['insurance_amount'] = $response['INSURANCEAMT'];
            $payment_detail['paypal_fee'] = !empty($payment_status['PAYMENTINFO_0_FEEAMT']) ? $payment_status['PAYMENTINFO_0_FEEAMT'] : '';
            $payment_detail['payment_date'] = $payment_status['TIMESTAMP'];
            $payment_detail['product_qty'] = $cart['items'][0]['qty'];
            $invoice = $this->createInvoice($cart, $status, $payment_detail);
            if ($invoice->paid) {
                session()->put(['code' => 'success', 'payment_message' => "Order $invoice->id has been paid successfully!"]);
            } else {
                session()->put(['code' => 'danger', 'message' => "Error processing PayPal payment for Order $invoice->id!"]);
            }
            return redirect('paypal/redirect-url');
        }
    }

    /**
     * @access protected
     * Set cart data for processing payment on PayPal.
     * @param bool $recurring
     * @return array
     */
    protected function getCheckoutData($recurring = false)
    {
        if (session()->has('product_id')) {
            $id = session()->get('product_id');
            $title = session()->get('product_title');
            $price = session()->get('product_price');
        }

        $user_id = Auth::user()->id;
        DB::table('orders')->insert(
            ['user_id' => $user_id, 'product_id' => $id, 'invoice_id' => null, 'status' => 'pending', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]
        );
        $custom_order_id = DB::getPdo()->lastInsertId();

        $random_number = Helper::generateRandomCode(4);
        $unique_code = strtoupper($random_number);
        $data = [];
        $order_id = Invoice::all()->count() + 1;
        if ($recurring === true) {
            $data['items'] = [
                [
                    'name' => 'Monthly Subscription ' . config('paypal.invoice_prefix') . ' #' . $order_id,
                    'price' => 0,
                    'qty' => 1,
                ],
            ];
            $data['return_url'] = url('/paypal/ec-checkout-success?mode=recurring');
            $data['subscription_desc'] = 'Monthly Subscription ' . config('paypal.invoice_prefix') . ' #' . $order_id;
        } else {
            $data['items'] = [
                [
                    'product_id' => $id,
                    'name' => $title,
                    'price' => $price,
                    'qty' => 1,
                    'custom_order_id' => $custom_order_id,
                ],
            ];
            $data['return_url'] = url('/paypal/ec-checkout-success');
        }
        $data['invoice_id'] = config('paypal.invoice_prefix') . '_' . $unique_code . '_' . $order_id;
        $data['invoice_description'] = "Order #$order_id Invoice";
        $data['cancel_url'] = url('/');
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $data['total'] = $total;

        // sent mail
        // Check email configuration - support both old and new Laravel config structure
        $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
        $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
        $mail_configured = !empty($mail_username) && !empty($mail_password);
        $email_settings_available = !empty(SiteManagement::getMetaValue('email_settings'));
        $mail_driver = config('mail.default');
        $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);
        
        if ($can_send_email) {
            $email_params = array();
            $role_type = array("superadmin", "reader");
            $superadmin = User::getUserByRoleType('superadmin');
            $super_admin_email = $superadmin[0]->email;

            $email_params['new_order_admin_email'] = $superadmin[0]->name;
            $email_params['new_order_id'] = $data['invoice_id'];
            $email_params['new_order_customer_name'] = Auth::user()->name;
            foreach ($role_type as $key => $role) {
                if ($role == "superadmin") {
                    $template_data = EmailTemplate::getEmailTemplatesByID($superadmin[0]->role_id, 'new_order');
                    if (!empty($template_data)) {
                        try {
                            Mail::to($super_admin_email)->send(new ArticleNotificationMailable($email_params, $template_data, $role));
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                } elseif ($role == "reader") {
                    $role_id = User::getRoleIDByUserID($user_id);
                    $customer_template_data = EmailTemplate::getEmailTemplatesByID($role_id, 'new_order');
                    if (!empty($customer_template_data)) {
                        try {
                            Mail::to(Auth::user()->email)->send(new ArticleNotificationMailable($email_params, $customer_template_data, $role));
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @access protected
     * Create invoice.
     * @param array  $cart
     * @param string $status
     * @param array $payment_detail
     * @return \App\Models\Invoice
     */
    protected function createInvoice($cart, $status, $payment_detail)
    {
        //create invoice
        $invoice = new Invoice();
        $invoice->title = htmlspecialchars($cart['invoice_description'], ENT_QUOTES, 'UTF-8');
        $invoice->price = $cart['total'];
        $invoice->payer_name = htmlspecialchars($payment_detail['payer_name'], ENT_QUOTES, 'UTF-8');
        $invoice->payer_email = filter_var($payment_detail['payer_email'], FILTER_SANITIZE_EMAIL);
        $invoice->seller_email = htmlspecialchars($payment_detail['seller_email'], ENT_QUOTES, 'UTF-8');
        $invoice->currency_code = htmlspecialchars($payment_detail['currency_code'], ENT_QUOTES, 'UTF-8');
        $invoice->payer_status = htmlspecialchars($payment_detail['payer_status'], ENT_QUOTES, 'UTF-8');
        $invoice->transaction_id = htmlspecialchars($payment_detail['transaction_id'], ENT_QUOTES, 'UTF-8');
        if (session()->has('product_vat')) {
            $tax = sprintf("%.2f", session()->get('product_vat'));
            $invoice->sales_tax = $tax;
        }
        $invoice->invoice_id = htmlspecialchars($payment_detail['invoice_id'], ENT_QUOTES, 'UTF-8');
        $invoice->shipping_amount = $payment_detail['shipping_amount'];
        $invoice->handling_amount = $payment_detail['handling_amount'];
        $invoice->insurance_amount = $payment_detail['insurance_amount'];
        $invoice->payment_mode = htmlspecialchars('paypal', ENT_QUOTES, 'UTF-8');
        $invoice->paypal_fee = $payment_detail['paypal_fee'];
        if (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) {
            $invoice->paid = 1;
        } else {
            $invoice->paid = 0;
        }
        $invoice->save();
        $invoice_id = DB::getPdo()->lastInsertId();
        // create item
        collect($cart['items'])->each(function ($product) use ($invoice) {
            $product_price = $invoice->price - $invoice->sales_tax;
            $item = new Item();
            $item->invoice_id = filter_var($invoice->id, FILTER_SANITIZE_NUMBER_INT);
            $item->product_id = filter_var($product['product_id'], FILTER_SANITIZE_NUMBER_INT);
            $item->item_name = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $item->item_price = $product_price;
            $item->item_qty = filter_var($product['qty'], FILTER_SANITIZE_NUMBER_INT);
            $item->save();
            $hits = 1;
            $user_id = Auth::user()->id;
            DB::table('downloads')->insert(
                ['user_id' => $user_id, 'product_id' => $product['product_id'], 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]
            );
            DB::table('orders')
                ->where('id', $product['custom_order_id'])
                ->update(['status' => 'completed']);

            //DB Query
            $article = DB::table('articles')->select('hits')->where('id', $product['product_id'])->first();
            if (($article->hits == 0)) {
                $final_count = 1;
            } else {
                $final_count = $article->hits + 1;
            }
            DB::table('articles')
                ->where('id', $product['product_id'])
                ->update(['hits' => $final_count]);
        });
        // sent mail
        // Check email configuration - support both old and new Laravel config structure
        $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
        $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
        $mail_configured = !empty($mail_username) && !empty($mail_password);
        $email_settings_available = !empty(SiteManagement::getMetaValue('email_settings'));
        $mail_driver = config('mail.default');
        $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);
        
        if ($can_send_email) {
            if (session()->has('product_price')) {
                $product_price = session()->get('product_price');
            }
            if (session()->has('product_title')) {
                $title = session()->get('product_title');
            }
            $super_admin = User::getUserByRoleType('superadmin');
            $super_admin_email = $super_admin[0]->email;
            $date = Carbon::parse($payment_detail['payment_date'])->format('F j, Y');
            if (session()->has('product_vat')) {
                $tax = sprintf("%.2f", session()->get('product_vat'));
                $invoice->sales_tax = $tax;
            } else {
                $tax = 0.00;
            }
            $email_params = array();
            $email_params['success_order_admin_name'] = $super_admin[0]->name;
            $email_params['success_order_product_title'] = $title;
            $email_params['success_order_invoice_id'] = $payment_detail['invoice_id'];
            $email_params['success_order_vat_amount'] = $tax;
            $email_params['success_order_gross_amount'] = $product_price;
            $email_params['success_order_total_amount'] = $cart['total'];
            $email_params['success_order_currency'] = $payment_detail['currency_code'];
            $email_params['success_order_payment_method'] = 'Paypal';
            $email_params['success_order_quantity'] = $payment_detail['product_qty'];
            $email_params['success_order_payment_date'] = $date;
            $email_params['success_order_customer_name'] = User::getUserNameByID(Auth::user()->id);
            $email_params['admin_success_order_link'] = url('/login?user_id=' . $super_admin[0]->id . '&email_type=success_order&invoice_id=' . $invoice_id);
            $email_params['customer_success_order_link'] = url('/login?user_id=' . Auth::user()->id . '&email_type=success_order&invoice_id=' . $invoice_id);
            $role_type = array("superadmin", "reader");
            foreach ($role_type as $key => $role) {
                if ($role == "superadmin") {
                    $template_data = EmailTemplate::getEmailTemplatesByID($super_admin[0]->role_id, 'success_order');
                    if (!empty($template_data)) {
                        try {
                            Mail::to($super_admin_email)->send(new ArticleNotificationMailable($email_params, $template_data, $role));
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                } elseif ($role == "reader") {
                    $reader_role_id = User::getRoleIDByUserID(Auth::user()->id);
                    $customer_template_data = EmailTemplate::getEmailTemplatesByID($reader_role_id, 'success_order');
                    if (!empty($customer_template_data)) {
                        try {
                            Mail::to(Auth::user()->email)->send(new ArticleNotificationMailable($email_params, $customer_template_data, $role));
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                }
            }
        }
        session()->forget('product_id');
        session()->forget('product_title');
        session()->forget('product_price');
        session()->forget('product_vat');
        return $invoice;
    }
}

