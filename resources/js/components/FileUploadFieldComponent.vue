<template>
  <div>
    <div class="sj-inputtyfile" v-bind:class="{ 'sj-uploading': this.uploading, 'sj-drag-over': isDragOver }"
         @dragover.prevent="onDragOver"
         @dragleave.prevent="onDragLeave"
         @drop.prevent="onDrop">
      <div class="sj-title">
        <h3>{{field_title}}</h3>
      </div>
      <label :for="this.doc_id" :id="'label'+doc_id">
        <span :class="{ 'uploaded_slider_image_name': slider_from_db }">{{ displayLabel }}</span>
        <div class="sj-filerightarea">
          <span v-if="fileSize">
            <em>{{fileSize}}</em>
          </span>
          <span v-else>
            <i v-bind:class="{ 'ti-upload': this.file_na, 'ti-close': this.file_check }"></i>
          </span>
        </div>
        <input
          type="file"
          :class="this.input_class"
          @change="notifyFileInput"
          :name="file_name"
          :id="doc_id"
          :ref="doc_ref"
        >
        <input v-if="slider_from_db && hidden_field_name" type="hidden" :value="slider_from_db" :name="hidden_field_name" :id="hidden_id">
      </label>
      <div class="sj-filedetails">
        <span>{{file_size_label}}</span>
        <em v-if="slider_from_db || file">{{file_uploaded_label}}</em>
        <em v-else>{{file_not_uploaded_label}}</em>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: [
    "field_title",
    "file_name",
    "file_placeholder",
    "file_size_label",
    "file_uploaded_label",
    "file_not_uploaded_label",
    "doc_id",
    "uploaded_file",
    "input_class",
    "hidden_field_name",
    "hidden_id"
  ],
  data() {
    return {
      file_check: false,
      file_na: true,
      file_error: true,
      file_completed: false,
      file: "",
      fileSize: "",
      uploading: false,
      isDragOver: false,
      doc_ref: "uploaded_doc",
      doc_input: "",
      file_object: "",
      deleted: "",
      slider_from_db: this.uploaded_file
    };
  },
  computed: {
    displayLabel() {
      if (this.slider_from_db || this.file) return this.slider_from_db || this.file;
      return this.file_placeholder;
    }
  },
  methods: {
    watchFileInput: function() {
      $('input[type="file"]').change(this.notifyFileInput.bind(this));
    },

    onDragOver() {
      this.isDragOver = true;
    },
    onDragLeave() {
      this.isDragOver = false;
    },
    onDrop(event) {
      this.isDragOver = false;
      var file = event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files[0];
      if (!file) return;
      var input = this.doc_id ? document.getElementById(this.doc_id) : (this.$refs[this.doc_ref] || null);
      if (input && typeof DataTransfer !== 'undefined') {
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    },

    notifyFileInput: function(event) {
      var file = event.target.files;
      this.file_object = file;
      var fileName = event.target.files[0].name;
      var Size = event.target.files[0].size;
      (this.file_check = false),
        (this.file_na = true),
        (this.file_error = true),
        (this.file_completed = false);

      if (fileName) {
        this.file_na = false;
        this.file_check = true;
        this.file_completed = true;
        this.file_error = false;
        this.uploading = true;
      }

      this.file = fileName;
      this.slider_from_db = fileName;
      let UploadedFileSize = this.bytesToSize(Size);
      this.fileSize = UploadedFileSize;
      this.deleted = "";
    },
    bytesToSize(bytes) {
      var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
      if (bytes == 0) return "0 Byte";
      var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    },
    clear: function() {
      console.log(this.file_object);
      this.file_object = null;
      this.file = "";
      this.file_check = false;
      this.file_na = true;
      this.file_error = true;
      this.fileSize = "";
      this.uploading = false;
      this.deleted = "deleted";
    }
  },

  created: function() {}
};
</script>
<style scoped>
.sj-inputtyfile.sj-drag-over {
  outline: 2px dashed #0066ff;
  outline-offset: 2px;
  background-color: rgba(0, 102, 255, 0.05);
}
</style>
