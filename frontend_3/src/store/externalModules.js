import {defineStore} from 'pinia';
import axios from 'axios';

export const useSharedExternalModules = defineStore('sharedEMObject', {
    state: () => ({
        totalFees: 0, // The shared object
        items_em: null, // list of enabled EMs
        isLoaded: false, // Whether the object has been loaded
        error: null, // Error message if any
        ajax_urls: window.ajax_urls || {},
    }),
    actions: {
        async loadExternalModules() {
            if (this.isLoaded) return this.items_em; // Return if already loaded

            try {
                const response = await axios.post(this.ajax_urls.get_project_external_modules);

                if (response.data.data !== undefined) {
                    this.items_em = this.allEms = response.data.data;
                    this.totalRows_em = this.items_em.length;

                    this.calculateTotalFees()
                    this.filterEms()
                }
                this.data = response.data.data;
                this.isLoaded = true;
                return response.data.data;
            } catch (err) {
                this.error = err.message || 'Failed to fetch data';
                console.error('Error loading shared object:', err);
                throw err;
            }
        },
        calculateTotalFees: function () {
            this.totalFees = 0
            for (var i = 0; i < this.items_em.length; i++) {
                this.totalFees += parseFloat(this.items_em[i].maintenance_fees)
            }

        },
        filterEms() {
            if (this.currentProjectEms === 'Yes') {
                this.items_em = this.allEms.filter(function (n) {
                    return n.maintenance_fees > 0 || n.maintenance_monthly_cost !== 'Module Disabled';
                });
            } else {
                this.items_em = this.allEms
            }

        },
        async getTotalFees () {

            if (!this.isLoaded){
                this.loadExternalModules()
            }
            return this.totalFees
        }
    },
});
