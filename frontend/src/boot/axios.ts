import { defineBoot } from '#q-app/wrappers';
import axios, { type AxiosInstance, type AxiosError } from 'axios';
import { Router } from 'vue-router';

declare module 'vue' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance;
    $api: AxiosInstance;
  }
}

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  config.headers.Accept = 'application/json';
  return config;
});

export default defineBoot(({ app, router }) => {
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = api;

  api.interceptors.response.use(
    (response) => response,
    (error: AxiosError) => {
      if (error.response?.status === 401) {
        localStorage.removeItem('token');
        void (router as Router).push('/login');
      }
      return Promise.reject(error);
    },
  );
});

export { api };
