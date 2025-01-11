import {defineStore} from 'pinia';
import axios from 'axios';

import { useOverlayStore } from './overlayStore.js';

export const useSharedPortalProject = defineStore('sharedObject', {
        state: () => ({
            data: null, // The shared object
            rma: [], // The shared object
            isLoaded: false, // Whether the object has been loaded
            error: null, // Error message if any
            ajax_urls: window.ajax_urls || {},
            projectState: null,
            PROD: 1,
            HAS_FEES: 2,
            LINKED: 4,
            HAS_RMA: 8,
            APPROVED_RMA: 16,
        }),
        actions: {
            async loadPortalProject() {
                useOverlayStore.showOverlay();
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
            async linked() {

                if (!this.isLoaded) {
                    await this.loadPortalProject()
                }

                if (this.data.project_portal_id !== '') {
                    return true;
                }
                return false;
            },
            async getProjectState() {
                if (this.projectState !== null) {
                    try {
                        // const response = await axios.post(window.ajax_urls.get_portal_project);
                        const response = await axios.post(this.ajax_urls.get_state);

                        this.projectState = response.state;

                    } catch (err) {
                        this.error = err.message || 'Failed to fetch data';
                        console.error('Error loading shared object:', err);
                        throw err;
                    }
                }
                return this.projectState
            },
            async determineREDCapStep() {
                this.rma = await this.get_rma()

                if (this.rma.id === undefined) {
                    return 1
                } else {
                    /**
                     APPROVED_PENDING_DEVELOPMENT = 2
                     APPROVED_ACTIVE_DEVELOPMENT = 6
                     APPROVED_MAINTENANCE = 7
                     * @type {number[]}
                     */
                    var statsus = [2, 6, 7]
                    if (this.rma.redcap !== undefined) {
                        return 2
                    }
                    if (!statsus.includes(this.rma.sow_status)) {
                        return 3
                    }
                    if (statsus.includes(this.rma.sow_status)) {
                        return 4
                    }
                    return 5
                }
            },
            async determineProjectAlert() {
                var alert = 'warning'

                if ((this.projectState & this.PROD) !== this.PROD && (this.projectState & this.HAS_FEES) !== this.HAS_FEES) {
                    return 'success'
                } else if ((this.projectState & this.PROD) !== this.PROD && this.projectState & this.HAS_FEES) {
                    if (this.projectState & this.LINKED && this.projectState & this.HAS_RMA && this.projectState & this.APPROVED_RMA) {
                        return 'success'
                    } else {
                        return 'warning'
                    }
                }
                if (this.projectState & this.PROD && (this.projectState & this.HAS_FEES) !== this.HAS_FEES) {
                    return 'success'
                } else if (this.projectState & this.PROD && this.projectState & this.HAS_FEES) {
                    if (this.projectState & this.LINKED && this.projectState & this.HAS_RMA && this.projectState & this.APPROVED_RMA) {
                        return 'success'
                    } else {
                        return 'danger'
                    }
                }
                return alert
            },
            async get_rma() {

                if (this.isLoaded) return this.rma; // Return if already loaded

                try {
                    // const response = await axios.post(window.ajax_urls.get_portal_project);
                    const response = await axios.post(this.ajax_urls.get_rma);

                    this.rma = response.data;
                    return this.rma
                } catch (err) {
                    this.error = err.message || 'Failed to fetch data';
                    console.error('Error loading shared object:', err);
                    throw err;
                }
            }
        },
    })
;
