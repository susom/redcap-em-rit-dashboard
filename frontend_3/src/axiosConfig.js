// src/axiosConfig.js
import axios from 'axios';
import { useOverlayStore } from './store/overlayStore.js';

// Ensure the store is accessible outside a Vue component
// const overlayStore = useOverlayStore();

// Request interceptor to show overlay
axios.interceptors.request.use(
  (config) => {
    useOverlayStore().showOverlay();
    return config;
  },
  (error) => {
    useOverlayStore().hideOverlay();
    return Promise.reject(error);
  }
);

// Response interceptor to hide overlay
axios.interceptors.response.use(
  (response) => {
    useOverlayStore().hideOverlay();
    return response;
  },
  (error) => {
    useOverlayStore().hideOverlay();
    return Promise.reject(error);
  }
);
