import {defineStore} from 'pinia';
import axios from 'axios';

export const useSharedPortalProject = defineStore('sharedObject', {
    state: () => ({
        data: null, // The shared object
        isLoaded: false, // Whether the object has been loaded
        error: null, // Error message if any
        ajax_urls: window.ajax_urls || {},
    }),
    actions: {
        async loadPortalProject() {
            console.log(this.isLoaded)
            console.log(this.data)
            if (this.isLoaded) return this.data; // Return if already loaded

            try {
               // const response = await axios.post(window.ajax_urls.get_portal_project);
                const response = await axios.post(this.ajax_urls.get_portal_project);

                this.data = response.data;
                this.isLoaded = true;
                return this.data
            } catch (err) {
                this.error = err.message || 'Failed to fetch data';
                console.error('Error loading shared object:', err);
                throw err;
            }
        },
        async linked () {

            if(!this.isLoaded){
                await this.loadPortalProject()
            }

            if (this.data.project_portal_id !== '') {
                return true;
            }
            return false;
        }
    },
});
