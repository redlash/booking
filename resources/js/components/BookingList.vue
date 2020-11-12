<template>
    <div class="card">
        <div class="card-header container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <h3>Booking records ({{ pagination.total }})</h3>
                </div>
                <div class="col-md-3 float-right" v-if="![null, undefined, ''].includes(user)">
                    <a class="btn btn-primary" @click="create">Book a meeting room</a>
                </div>
            </div>
        </div>

        <div class="card-body">

            <h4 class="text-info text-center" v-if="records.length === 0">No booking records.</h4>

            <div class="form-group row" v-if="errorVisible">
                <div class="col-md-10 offset-1">
                    <div class="alert alert-danger" role="alert">
                        <span class="sr-only">Error:</span>
                        {{ error }}
                    </div>
                </div>
            </div>

            <div class="form-group row" v-if="messageVisible">
                <div class="col-md-10 offset-1">
                    <div class="alert alert-success" role="alert">
                        <span class="sr-only">Success:</span>
                        {{ message }}
                    </div>
                </div>
            </div>

            <div class="row mt-3" id="filters-container" v-if="records.length > 0 || hasFilters()">
                <div class="col-md-3">
                    <select v-model="state.filters.user_id" @change="filterChanged"
                            id="filter_by_user" class="form-control" name="filter_by_user">
                        <option value="">-- Filter by User --</option>
                        <option v-for="usr in users" :value="usr.id">{{ usr.name }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select v-model="state.filters.meeting_room_id" @change="filterChanged"
                            id="filter_by_meeting_room" class="form-control" name="filter_by_meeting_room">
                        <option value="">-- Filter by Meeting Room --</option>
                        <option v-for="meetingRoom in meetingRooms" :value="meetingRoom.id">{{ meetingRoom.name }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select v-model="state.filters.date_from" @change="filterChanged"
                            id="filter_by_date" class="form-control" name="filter_by_date">
                        <option value="">-- From Date --</option>
                        <option v-for="date in dates" :value="date">{{ date }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select v-model="state.filters.date_to" @change="filterChanged"
                            id="filter_by_date_end" class="form-control" name="filter_by_date_end">
                        <option value="">-- To Date --</option>
                        <option v-for="date in dates" :value="date">{{ date }}</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row mb-3" v-if="records.length > 0">
                <div class="col-md-3 text-center">
                    <span><a @click="sortAsc('date')"><strong>&#94;</strong></a></span>
                    &nbsp;&nbsp;&nbsp;<strong>Date</strong>&nbsp;&nbsp;
                    <span style="font-size: 24px"><a @click="sortDesc('date')"><strong>&#8964;</strong></a></span>
                </div>
                <div class="col-md-3 text-center">
                    <span><a @click="sortAsc('user')"><strong>&#94;</strong></a></span>
                    &nbsp;&nbsp;&nbsp;<strong>Owner</strong>&nbsp;&nbsp;
                    <span style="font-size: 24px"><a @click="sortDesc('user')"><strong>&#8964;</strong></a></span>
                </div>
                <div class="col-md-3 text-center">
                    <span><a @click="sortAsc('meeting_room')"><strong>&#94;</strong></a></span>
                    &nbsp;&nbsp;&nbsp;<strong>Meeting room</strong>&nbsp;&nbsp;
                    <span style="font-size: 24px"><a @click="sortDesc('meeting_room')"><strong>&#8964;</strong></a></span>
                </div>
                <div class="col-md-3 text-center" v-if="![null, undefined, ''].includes(user)"><strong>Actions</strong></div>
            </div>
            <hr>

            <div class="row mb-2"  v-if="records.length > 0"
                 v-for="record in records" :id="'booking-' + record.id">
                <div class="col-md-3 text-center">{{ record.occupy_at }} ({{ record.start_at }}-{{ record.end_at }})</div>
                <div class="col-md-3 text-center">{{ record.user.name }}</div>
                <div class="col-md-3 text-center">{{ record.meeting_room.name }}</div>
                <div class="col-md-3 text-center" v-if="![null, undefined, ''].includes(user) && record.user.id === user.id">
                    <a class="btn btn-info mr-1" @click="edit(record)">Edit</a>
                    <a class="btn btn-danger" @click="cancel(record)">Cancel</a>
                </div>
            </div>
            <hr>

            <div class="row justify-content-center mt-3" id="pagination-container" v-if="records.length > 0 && pagination.links.length > 3">
                <nav aria-label="pagination">
                    <ul class="pagination">
                        <li :class="{ 'page-item': true, disabled: pagination.current_page === 1 }">
                            <a class="page-link" @click="pageChanged($event, pagination.prev_page_url)">Previous</a>
                        </li>
                        <li v-for="link in pagination.links"
                            :class="{ 'page-item': true, active: link.active }"
                            v-if="['Previous', 'Next'].includes(link.label) === false">
                            <a class="page-link" @click="pageChanged($event, link.url)">{{ link.label }}</a>
                        </li>
                        <li :class="{ 'page-item': true, disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" @click="pageChanged($event, pagination.next_page_url)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</template>

<script>
export default {

    created() {

        const vm = this;

        vm.getRecords();
        vm.getMeetingRooms();
        vm.getUsers();
    },

    mounted() {
        console.log('bookings mounted.')
    },

    props: ['type', 'user'],

    data: function () {

        let d = new Date(), dates = [];
        for (let i=0;i<31;i++) {
            dates[i] = `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;
            d.setDate(d.getDate() + 1);
        }

        return {
            users: [],
            meetingRooms: [],
            records: [],
            pagination: {links: [], total: 0},
            dates: dates,
            state: {
                filters:{
                    user_id: '',
                    meeting_room_id: '',
                    date_from: '',
                    date_to: ''
                },
                sort_by: ''
            },
            errorVisible: false,
            messageVisible: false,
            message: '',
            error: ''
        }
    },

    methods: {

        getMeetingRooms: function() {

            const vm = this;

            fetch('/api/meeting_rooms')
                .then(res => res.json())
                .then(payload => {
                    console.log(payload);
                    vm.meetingRooms = payload;
                })
                .catch(err => console.log(err))
        },

        getUsers: function () {

            const vm = this;

            fetch('/api/users')
                .then(res => res.json())
                .then(payload => {
                    console.log(payload);
                    vm.users = payload;
                })
                .catch(err => console.log(err))
        },

        /**
         * When url not null, it is a page url.
         */
        getRecords: function (url = null) {

            let vm = this;

            let params = vm.getParams();

            const endpoint = url === null
                ? (params.length === 0 ? '/api/bookings': `/api/bookings?${params}`)
                : `${url}&${params}`

            fetch(endpoint)
                .then(res => res.json())
                .then(payload => {
                    console.log(payload);
                    vm.records = payload.data;
                    vm.pagination = {
                        current_page: payload.current_page,
                        last_page: payload.last_page,
                        first_page_url: payload.first_page_url,
                        next_page_url: payload.next_page_url,
                        prev_page_url: payload.prev_page_url,
                        links: payload.links,
                        total: payload.total
                    }
                })
                .catch(err => console.log(err))
        },

        filterChanged: function (event) {

            const vm = this;

            vm.getRecords();
        },

        pageChanged: function (event, url) {

            const vm = this;
console.log(`pageChanged: ${url}`)
            vm.getRecords(url)
        },

        sortDesc: function (param) {

            const vm = this;

            vm.state.sort_by = `sort_by=${param}.desc`;
            console.log(`sortDesc: ${param}`)
            vm.getRecords();
        },

        sortAsc: function (param) {

            const vm = this;

            vm.state.sort_by = `sort_by=${param}.asc`;
            console.log(`sortAsc: ${param}`)
            vm.getRecords();
        },

        create: function (event) {

            const vm = this;

            vm.$emit('create');

            console.log('Handle create...')
        },

        edit: function (record) {

            const vm = this;

            vm.$emit('edit', record);

            console.log('Handle edit...')
        },

        save: function (event, record) {

            const vm = this;

            if (vm.user === null) {
                return;
            }

            vm.errorVisible = false;
            vm.error = '';

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
                    vm.records = data;
                })
                .catch(err => {
                    vm.error = err;
                    vm.errorVisible = true;
                })
        },

        cancel: function (record) {

            const vm = this;

            if (vm.user === null) {
                return;
            }

            if (confirm('Are you sure you want to cancel this booking?')) {

                vm.errorVisible = false;
                vm.error = '';
                vm.messageVisible = false;
                vm.message = '';

                fetch(`api/booking/${record.id}`, {
                    method: 'delete',
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                    .then(payload => {
                        console.log(payload);
                        if ('error' in payload) {
                            vm.error = payload.error;
                            vm.errorVisible = true;
                            return;
                        }
                        vm.records = payload.data.data;
                    })
                    .catch(err => {
                        vm.error = err;
                        vm.errorVisible = true;
                    })
            }
        },

        /**
         * Generate parameters for filters and sorting.
         *
         * @param event
         */
        getParams: function () {

            const vm = this;

            let params = '';
            /* Append filter params. */
            for (const key in vm.state.filters) {

                if (['', null].includes(vm.state.filters[key])) {
                    continue;
                }

                params = params.length === 0
                    ? `${key}=${vm.state.filters[key]}`
                    : `${params}&${key}=${vm.state.filters[key]}`;
                console.log(params);
            }

            /* Append sorting params. */
            params = params.length === 0
                ? `${vm.state.sort_by}`
                : `${params}&${vm.state.sort_by}`;
            console.log(`getParams: ${params}`);
            return params;
        },

        hasFilters: function () {

            const vm = this;

            for (const key in vm.state.filters) {

                if (! ['', null].includes(vm.state.filters[key])) {
                    return true;
                }
            }
            return false;
        }
    }
}
</script>