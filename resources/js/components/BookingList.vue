<template>

    <div class="card">
        <div class="card-header"><h3>Booking records</h3></div>

        <div class="card-body text-center" v-if="records.length === 0">
            <h4 class="text-info">No booking records.</h4>
        </div>

        <div class="card-body" v-if="records.length > 0">

            <div class="row mb-3">
                <div class="col-md-2">Filters: </div>
                <div class="col-md-4">Meeting room</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 text-center"><strong>Date</strong></div>
                <div class="col-md-2 text-center"><strong>Meeting room</strong></div>
                <div class="col-md-2 text-center" v-if="user !== null"><strong>Actions</strong></div>
            </div>

            <div class="row mb-2" v-for="record in records" :id="'booking-' + record.id">
                <div class="col-md-6">{{ record.occupy_at }} {{ record.start_at }}-{{ record.end_at }}</div>
                <div class="col-md-2">{{ record.meeting_room.name }}</div>
                <div class="col-md-2" v-if="user !== null">
                    <a class="btn btn-info" @click="edit(record)">Edit</a>
                    <a class="btn btn-warning" @click="cancel(record)">Cancel</a>
                </div>
            </div>

            <div class="row mb-2">
                <nav aria-label="pagination">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    created() {

        this.getRecords();
    },

    mounted() {
        console.log('bookings mounted.')
    },

    props: ['type', 'user'],

    data: function () {

        return {
            records: [],
            pagination: {},
            showEditForm: false,
        }
    },

    methods: {

        getRecords: function () {

            let vm = this;

            fetch('/api/bookings')
                .then(res => res.json())
                .then(payload => {
                    console.log(payload);
                    vm.records = payload.data;
                    vm.pagination = {
                        current_page: payload.current_page,
                        first_page_url: payload.first_page_url,
                        links: payload.links
                    }
                })
                .catch(err => console.log(err))
        },

        edit: function (event, record) {
            this.showEditForm = true;
            console.log('Handle edit...')
        },

        save: function (event, record) {

            const vm = this;

            event.preventDefault();

            if (vm.user === null) {
                return;
            }

            fetch(`api/booking/${record.id}`, {
                method: 'put',
                mode: 'cors',
                headers: {
                    'Authorization': `Bearer ${vm.user.api_token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(vm.data)
            }).then(res => res.json())
                .then(data => {
                    console.log(data);
                    //vm.records = data;
                })
                .catch(err => console.log(err))
        },

        cancel: function (event, record) {

            const vm = this;

            event.preventDefault();

            if (vm.user === null) {
                return;
            }

            if (confirm('Are you sure?')) {
                fetch(`api/booking/${record.id}`, {
                    method: 'delete',
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                    .then(data => {
                        console.log(data);
                        //vm.records = data;
                    })
                    .catch(err => console.log(err))
            }
        }
    }
}
</script>