<template>
    <admin-table
        title="Media"
        :data="media"
        :columns="columns"
        :loading="loading"
        @filter="filter"
    />
</template>

<script>
import AdminTable from '../../../vendor/kieranfyi/admin/resources/js/components/AdminTable';
export default {
    components: {
        AdminTable
    },
    data() {
        return {
            loading: true,
            media: {
                data: null,
                links: null
            },
            columns: {
                file_name: 'Name',
                key: 'key',
                created_at: {
                    type: 'date',
                    label: 'Created'
                }
            },
            searchText: null,
            resultsPage: 1
        };
    },
    methods: {
        show(item) {
            console.log(item);
        },
        fetchData(data) {
            this.loading = true;
            this.$axios
                .post(route('admin.api.media.search'), data)
                .then((response) => {
                    this.media = response.data;
                    for (const [key, media] of Object.entries(this.media.data)) {
                        let actions = [];
                        // if (media.access.show) {
                        //     actions.push({
                        //         icon: 'fas fa-eye',
                        //         click: this.show,
                        //         class: 'btn-outline-primary'
                        //     });
                        // }
                        this.media.data[key]['actions'] = actions;
                    }

                    this.loading = false;
                });
        },
        filter(data) {
            this.loading = true;
            this.fetchData(data);
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