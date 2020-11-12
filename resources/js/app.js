/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('booking-list', require('./components/BookingList.vue').default);
Vue.component('booking-form', require('./components/BookingForm.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: {
        listVisible: true,
        formVisible: false,
        bookingRecord: null
    },
    methods: {

        /**
         * Show create form, remove list.
         *
         * @param event
         */
        showNewForm: function (event) {

            const instance = this;

            instance.listVisible = false;
            instance.formVisible = false;
            /* Clear bookingRecord model. */
            instance.bookingRecord = null;
            Vue.nextTick(() => {
                instance.formVisible = true;
            })
        },

        /**
         * Show edit form, hide list.
         *
         * @param event
         * @param record
         */
        showEditForm: function (record) {

            const instance = this;

console.log(`showEditForm:`); console.log(record);
            instance.bookingRecord = record;
            instance.listVisible = false;
            instance.formVisible = false;

            Vue.nextTick(() => {
                instance.formVisible = true;
            })
        },

        /**
         * After a booking record is created, render the list and
         * hide the form.
         *
         * @param event
         */
        handleCreated: function (event) {

            const instance = this;

            instance.listVisible = false;
            instance.formVisible = false;
            Vue.nextTick(() => {
                instance.listVisible = true;
            })
        },

        /**
         * After a booking record is updated, show the hidden list and
         * update the record.
         *
         * @param event
         * @param record
         */
        handleUpdated: function (event, record) {

            const instance = this;

            instance.listVisible = false;
            instance.formVisible = false;
            instance.bookingRecord = null;

            Vue.nextTick(() => {
                instance.listVisible = true;
            })
        },

        /**
         * Close the form and show the list.
         *
         * @param event
         */
        handleCanceled: function (event) {

            const instance = this;
            console.log(`handleCanceled:`);

            instance.formVisible = false;
            instance.listVisible = false;

            Vue.nextTick(() => {
                instance.listVisible = true;
            })
        }
    }
});
