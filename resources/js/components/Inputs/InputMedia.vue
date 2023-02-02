<template>
    <div :class="{
        'form-group': true,
         row: label !== undefined
        }">
        <label v-if="label !== undefined" class="col-lg-2 text-lg-right">
            {{ label }}
        </label>
        <div :class="{'col-lg-10': label !== undefined}">
            <admin-table :columns="columns" :rows="media" :small="true" :head="false" v-if="media.length"
                         action-location="start" :loading="_loading"/>
            <input-media-upload :name="name" :value="value" :loading="loading" :options="options"
                                v-if="multiple || (!multiple && media.length < 1)"
                                @updated="updated"/>
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
        values: {
            type: Array,
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
            media: [],
            _loading: true,
            columns: {
                file_name: ''
            }
        };
    },
    computed: {
        multiple() {
            if (this.options === undefined || this.options === null || this.options.multiple !== null) {
                return false;
            }
            return this.options.multiple;
        },
        _values() {
            if (this.options === undefined || this.options === null || this.options.multiple !== null) {
                if (this.value === undefined) {
                    return [];
                }
                return [this.value];
            }

            if (this.values === undefined) {
                return [];
            }
            return this.values;
        }
    },
    methods: {
        fetchData() {
            this._loading = true;
            let values = this._values;
            if (values !== null) {
                for (const media of values) {
                    if (media === null) {
                        continue;
                    }
                    this.$axios
                        .get(route('admin.api.media.show', {media}))
                        .then((response) => {
                            let media = response.data;
                            let actions = [];
                            actions.push({
                                icon: 'fas fa-times',
                                click: this.delete,
                                class: 'btn btn-link btn-sm text-danger px-1 py-0'
                            });
                            media['actions'] = actions;
                            this.media.push(media);
                            this._loading = false;
                        });
                }
            }
        },
        updated(key, value) {
            this._loading = true;
            if (this.multiple) {
                this.$emit('updated', key, value.concat(this._values));
                this.fetchData();
                return;
            }
            this.$emit('updated', key, value);
            this.fetchData();
        },
        delete(media) {
            this._loading = true;
            this.$axios
                .delete(route('admin.api.media.show', {media}))
                .then(function () {
                    let values = this._values;
                    let index = values.indexOf(media.id);
                    if (index > -1) {
                        values.splice(index, 1);
                    }
                    if (this.multiple) {
                        this.$emit('updated', this.key, values);
                        return;
                    }

                    if (values.length > 0) {
                        this.$emit('updated', this.key, values[0]);
                        return;
                    }

                    this.$emit('updated', this.key, null);
                });
        }
    },
    created() {
        this.fetchData = this.$lodash.debounce(this.fetchData, 500);
    },
    mounted() {
        this.fetchData();
    }
}
</script>