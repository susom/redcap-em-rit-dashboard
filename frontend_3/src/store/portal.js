import {defineStore} from 'pinia';
import axios from 'axios';

export const useSharedPortalProject = defineStore('sharedObject', {
    state: () => ({
        data: null, // The shared object
        isLoaded: false, // Whether the object has been loaded
        error: null, // Error message if any
    }),
    actions: {
        async loadPortalProject() {
             console.log('loadPortalProject method')
             console.log('loadPortalProject method')
            if (this.isLoaded) return this.data; // Return if already loaded

            try {
               // const response = await axios.post(window.ajax_urls.get_portal_project);
                const response = await axios.post('http://redcap.local/redcap_v14.9.1/ExternalModules/?prefix=rit_dashboard&page=ajax/portal/get_portal_project&NOAUTH&pid=164');
                this.data = response.data;
                this.isLoaded = true;
                return this.data;
            } catch (err) {
                this.error = err.message || 'Failed to fetch data';
                console.error('Error loading shared object:', err);
                throw err;
            }
        },
        linked: function () {
            console.log('linked method')
            if(!this.isLoaded){
                this.loadPortalProject()
            }

            if (this.data.project_portal_id !== '' && this.data.project_portal_id_saved === "true") {
                return true;
            }
            return false;
        }
    },
});
