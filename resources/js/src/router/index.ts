import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'

import Home from '@/views/Home.vue'
import Documents from '@/views/Documents.vue'
import Upload from '@/views/Upload.vue'
import DocumentView from '@/views/DocumentView.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import Profile from '@/views/Profile.vue'
import ForgotPassword from '@/views/ForgotPassword.vue'
import ResetPassword from '@/views/ResetPassword.vue'

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home
	},
	{
		path: '/documents',
		name: 'Documents',
		component: Documents,
		meta: { requiresAuth: true }
	},
	{
		path: '/upload',
		name: 'Upload',
		component: Upload,
		meta: { requiresAuth: true }
	},
	{
		path: '/document/:id',
		name: 'DocumentView',
		component: DocumentView,
		props: true,
		meta: { requiresAuth: true }
	},
	{
		path: '/login',
		name: 'Login',
		component: Login,
		meta: { guestOnly: true }
	},
	{
		path: '/register',
		name: 'Register',
		component: Register,
		meta: { guestOnly: true }
	},
	{
		path: '/forgot-password',
		name: 'ForgotPassword',
		component: ForgotPassword,
		meta: { guestOnly: true }
	},
	{
		path: '/password-reset',
		name: 'ResetPassword',
		component: ResetPassword,
		meta: { guestOnly: true }
	},
	{
		path: '/profile',
		name: 'Profile',
		component: Profile,
		meta: { requiresAuth: true }
	}
]

const router = createRouter({
	history: createWebHistory(),
	routes
})

router.beforeEach((to, from, next) => {
	const authStore = useAuthStore()
	const requires_auth = to.matched.some(record => record.meta.requiresAuth)
	const guest_only = to.matched.some(record => record.meta.guestOnly)

	if (requires_auth && !authStore.is_authenticated) {
		next('/login')
	} else if (guest_only && authStore.is_authenticated) {
		next('/')
	} else {
		next()
	}
})

export default router