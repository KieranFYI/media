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
                         action-location="start"/>
            <input-media-upload :name="name" :value="value" :loading="loading" :options="options"
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
        _values() {
            if (this.options.multiple) {
                return this.values;
            } else {
                return [this.value];
            }
        }
    },
    methods: {
        fetchData() {
            this._loading = true;
            for (const media of this._values) {
                this.$axios
                    .get(route('admin.api.media.show', {media}))
                    .then((response) => {
                        let media = response.data;
                        let actions = [];
                        if (media.access.show) {
                            actions.push({
                                icon: 'fas fa-times',
                                click: this.delete,
                                class: 'btn btn-link btn-sm text-danger'
                            });
                        }
                        media['actions'] = actions;
                        this.media.push(media);
                        this._loading = false;
                    });
            }
        },
        updated(key, value) {
            this.$emit('updated', key, value);
        },
        delete(item) {
            console.log(item);
        }
    },
    created() {
        this.fetchData = this.$lodash.debounce(this.fetchData, 500);
    },
    mounted() {
        this.fetchData();
    },
}
</script>