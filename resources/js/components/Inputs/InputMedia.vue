<template>
    <div :class="{
        'form-group': true,
         row: label !== undefined
        }">
        <label :for="id" v-if="label !== undefined" class="col-lg-2 text-lg-right">
            {{ label }}
        </label>
        <div :class="{'col-lg-10': label !== undefined}">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button class="btn btn-danger" type="button">Delete</button>
                </div>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" :id="id" :multiple="multiple" @change="upload" :disabled="uploading">
                    <label class="custom-file-label" :id="id" v-text="status"></label>
                </div>
            </div>

            <span class="invalid-feedback d-block" role="alert" v-for="error in uploadErrors">
                {{ error }}
            </span>
            <span class="invalid-feedback d-block" role="alert" v-for="error in errors">
                {{ error }}
            </span>
        </div>
    </div>
</template>

<script>
export default {
    emits: [
        'updated'
    ],
    props: {
        label: {
            type: String
        },
        name: {
            type: String,
            required: true
        },
        value: {
            type: Number
        },
        errors: {
            type: Array
        },
        loading: {
            type: Boolean
        },
        accepts: {
            type: Array,
            default: []
        },
        multiple: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            id: null,
            status: 'Choose file',
            uploading: false,
            uploadErrors: [],
        };
    },
    methods: {
        upload(event) {
            this.uploading = true;
            this.status = 'Uploading';
            let formData = new FormData();
            for (const file of event.target.files) {
                formData.append("files[]", file);
            }

            this.$axios
                .post(route('admin.api.media.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        let percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        this.status = 'Uploading ' + percentCompleted + '%';
                    }
                })
                .then((response) => {
                    if (this.multiple) {
                        this.status = 'Files uploaded';
                        this.$emit('updated', this.name, response.data);
                    } else {
                        let media = response.data[0];
                        this.status = media.file_name + ' uploaded';
                        this.$emit('updated', this.name, media);
                    }
                })
                .catch((error) => {
                    if (!error.response) {
                        return;
                    }
                    if (!error.response.data.errors) {
                        if (error.response.data.message) {
                            alert(error.response.data.message);
                        } else {
                            alert('An unknown error has occurred');
                        }
                        return;
                    }

                    Object.keys(error.response.data.errors).forEach(key => {
                        this.uploadErrors.push(error.response.data.errors[key][0]);
                    });
                })
                .finally(() => {
                    this.uploading = false;
                });
        }
    },
    created() {
        this.id = Math.random().toString(36).replace(/[^a-z]+/g, '');
    },
}
</script>