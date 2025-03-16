import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { axiosError } from '@/helpers'
import axiosUser from '@/axios'

export interface User {
	id: string
	name: string
	email: string
	token: string
}

export interface UserUpdate {
	name: string
	email: string
}

export interface LoginData {
	email: string
	password: string
}

export interface ForgotPasswordData {
	email: string
}

export interface ResetPasswordData {
	token: string,
	password: string
}

export interface RegisterData {
	name: string
	email: string
	password: string
}

interface LoginApiResponse {
	user: {
		id: number;
		name: string;
		email: string;
		email_verified_at: string | null;
		created_at: string
		updated_at: string
	};
	token: string;
}

export const useAuthStore = defineStore('auth', () => {
	const user = ref<User | null>(null)
	const loading = ref(false)
	const errors = ref<any>(null);

	const is_authenticated = computed(() => !!user.value?.token)

	async function login(formdata: LoginData) {
		loading.value = true
		errors.value = null

		try {
			await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`);

			const response = await axios.post<LoginApiResponse>(
				`${import.meta.env.VITE_API_URL}/api/auth/login`,
				formdata
			);

			loading.value = false;

			const res_data = response.data;

			if (res_data) {
				user.value = {
					id: res_data.user.id.toString(),
					name: res_data.user.name,
					email: res_data.user.email,
					token: res_data.token
				}

				return true
			}

		} catch (error) {
			loading.value = false
			errors.value = axiosError(error)

			return false
		}
	}

	async function register(formdata: RegisterData) {
		loading.value = true
		errors.value = null

		try {
			await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`);

			const response = await axios.post<LoginApiResponse>(
				`${import.meta.env.VITE_API_URL}/api/auth/register`,
				formdata
			);

			loading.value = false;

			const res_data = response.data;

			if (res_data) {
				user.value = {
					id: res_data.user.id.toString(),
					name: res_data.user.name,
					email: res_data.user.email,
					token: res_data.token
				}

				return true
			}

		} catch (error) {
			loading.value = false
			errors.value = axiosError(error)

			return false
		}
	}

	async function logout() {
		errors.value = null
		loading.value = true;

		try {
			await axiosUser.get(`/sanctum/csrf-cookie`);

			const response = await axiosUser.post(`/api/auth/logout`, {});


			loading.value = false;

			if (response.status == 204) {
				user.value = null

				return true
			}

		} catch (error) {
			loading.value = false
			errors.value = axiosError(error)

			return false
		}
	}

	async function udpateProfile(id: number, formdata: UserUpdate) {
		loading.value = true
		errors.value = null

		try {
			await axiosUser.get(`/sanctum/csrf-cookie`);

			const response = await axiosUser.post(
				`/api/users/${id}`,
				formdata
			);

			loading.value = false;

			const res_data = response.data;

			if (res_data) {
				user.value = {
					id: res_data.id.toString(),
					name: res_data.name,
					email: res_data.email,
					token: user.value?.token ?? ""
				}

				return true
			}

		} catch (error) {
			loading.value = false
			errors.value = axiosError(error)

			return false
		}
	}

	async function forgotPassword(formdata: ForgotPasswordData) {
		loading.value = true
		errors.value = null

		try {
			await axiosUser.get(`/sanctum/csrf-cookie`);

			const response = await axiosUser.post(
				`/api/auth/forgot-password`,
				formdata
			);

			loading.value = false;

			if (response.status == 204) return true


		} catch (error) {
			loading.value = false
			errors.value = axiosError(error)

			return false
		}
	}

	async function resetPassword(formdata: ResetPasswordData) {
		loading.value = true
		errors.value = null

		try {
			await axiosUser.get(`/sanctum/csrf-cookie`);

			const response = await axiosUser.post(
				`/api/auth/password-reset`,
				formdata
			);

			loading.value = false;

			if (response.status == 204) return true

		} catch (error) {
			loading.value = false;

			if (axios.isAxiosError(error) && error.response) {
				if (error.response.status == 422) {
					errors.value = { 'status': '422', 'errors': error.response.data.errors };
				} else if (error.response.status == 419) {
					errors.value = { 'status': '419', 'message': 'Page expirée. Veuillez actualiser la page' }
				} else if (error.response.status == 400) {
					errors.value = { 'status': '400', 'message': 'Code incorrect ou code expiré' }
				}
			} else {
				errors.value = { 'status': 'none', 'message': 'Une erreur s\'est produite. Veuillez réessayer' }
			}

			return false
		}
	}

	return {
		user,
		loading,
		errors,
		is_authenticated,
		login,
		register,
		logout,
		udpateProfile,
		forgotPassword,
		resetPassword
	}
}, {
	persist: {
		storage: localStorage,
		paths: ['user']
	}
})
