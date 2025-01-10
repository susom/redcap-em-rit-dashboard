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
             console.log('loadPortalProject method')
            if (this.isLoaded) return this.data; // Return if already loaded

            try {
               // const response = await axios.post(window.ajax_urls.get_portal_project);
                const response = await axios.post(this.ajax_urls.get_portal_project);
                console.log("response")
                console.log(response)
                this.data = response.data;
                this.isLoaded = true;
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
            console.log('linked method')
            console.log(this.data)
            if (this.data.project_portal_id !== '') {
                return true;
            }
            return false;
        }
    },
});
