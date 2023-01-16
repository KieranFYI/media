<template>
    <div :class="{
        'form-group': !this.child,
         row: label !== undefined
        }">
        <label :for="id" v-if="label !== undefined && !this.child" class="col-lg-2 text-lg-right">
            {{ label }}
        </label>
        <div :class="{'col-lg-10': label !== undefined && !this.child}">
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" :id="id" :multiple="multiple"
                           :accept="accepts" @change="upload" :disabled="uploading || loading">
                    <label class="custom-file-label" :id="id" v-text="loading ? 'Loading' : status"></label>
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
        child: {
            type: Boolean,
            default: false
        },
        errors: {
            type: Array
        },
        loading: {
            type: Boolean
        },
        options: {
            type: Object,
            default: {
                accepts: [],
                multiple: false
            }
        },
    },
    data() {
        return {
            id: null,
            status: 'Choose a file...',
            uploading: false,
            uploadErrors: [],
        };
    },
    computed: {
        multiple() {
            if (this.options === undefined || this.options === null || this.options.multiple !== null) {
                return false;
            }
            return this.options.multiple;
        },
        accepts() {
            if (this.options === undefined || this.options === null || this.options.accepts !== null) {
                return [];
            }
            return this.options.accepts;
        }
    },
    methods: {
        upload(event) {
            this.uploading = true;
            this.post(event);
        },
        post(event) {
            this.uploading = true;
            this.status = 'Uploading';
            let formData = new FormData();
            for (const file of event.target.files) {
                formData.append("files[]", file);
            }
            let $this = this;
            this.$axios
                .post(route('admin.api.media.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function (progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        $this.status = 'Uploading ' + percentCompleted + '%';
                    }
                })
                .then((response) => {
                    if (this.multiple) {
                        let ids = this.$lodash.map(response.data, 'id');
                        this.$emit('updated', this.name, ids);
                    } else {
                        let media = response.data[0];
                        this.$emit('updated', this.name, media.id);
                    }
                    this.status = 'Choose a file...';
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
        this.upload = this.$lodash.debounce(this.upload, 500);
    },
    mounted() {
        this.status = 'Choose a file...';
    }
}
</script>