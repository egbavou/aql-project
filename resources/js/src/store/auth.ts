import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface User {
	id: string
	name: string
	email: string
	token: string
}

export interface LoginData {
	email: string
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
			await axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie");

			const response = await axios.post<LoginApiResponse>(
				'http://127.0.0.1:8000/api/auth/login',
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
			loading.value = false;

			if (axios.isAxiosError(error) && error.response) {
				if (error.response.status == 419) {
					errors.value = { 'status': '419', 'message': 'Page expirée. Veuillez actualiser la page' }
				} else if (error.response.status == 401) {
					errors.value = { 'status': '401', 'message': 'Email ou mot de passe incorrect' }
				}
			} else {
				errors.value = { 'status': 'none', 'message': 'Une erreur s\'est produite. Veuillez réessayer' }
			}

			return false
		}
	}

	async function register(formdata: RegisterData) {
		loading.value = true
		errors.value = null

		try {
			await axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie");

			const response = await axios.post<LoginApiResponse>(
				'http://127.0.0.1:8000/api/auth/register',
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
			loading.value = false;

			if (axios.isAxiosError(error) && error.response) {
				if (error.response.status == 422) {
					errors.value = { 'status': '422', 'errors': error.response.data.errors };
				} else if (error.response.status == 419) {
					errors.value = { 'status': '419', 'message': 'Page expirée. Veuillez actualiser la page' }
				} else if (error.response.status == 401) {
					errors.value = { 'status': '401', 'message': 'Email ou mot de passe incorrect' }
				}
			} else {
				errors.value = { 'status': 'none', 'message': 'Une erreur s\'est produite. Veuillez réessayer' }
			}

			return false
		}
	}

	async function logout() {
		errors.value = null

		try {

			var headers = {
				"Accept": "application/json",
				"Content-Type": "application/json",
				"Authorization": `Bearer ${user.value?.token}`
			}
			await axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie", {
				headers: headers
			});

			const response = await axios.post('http://127.0.0.1:8000/api/auth/logout', {}, {
				headers: headers
			});

			console.log(1)
			if (response.status == 404) {
				user.value = null
				console.log(2)

				return true
			}

		} catch (error) {
			loading.value = false;

			if (axios.isAxiosError(error) && error.response) {
				if (error.response.status == 419) {
					errors.value = { 'status': '419', 'message': 'Page expirée. Veuillez actualiser la page' }
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
		logout
	}
}, {
	persist: {
		storage: localStorage,
		paths: ['user']
	}
})