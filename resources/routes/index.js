// const AuthLayout = () => import("@/Layouts/AuthLayout.vue");
const About = () => import("@/Pages/About.vue");
const Forms = () => import("@/pages/Forms.vue");
const Home = () => import("@/Pages/HomePage.vue");
const MainLayout = () => import("@/Layouts/MainLayout.vue");
const LoginPage = () => import("@/Pages/Auth/LoginPage.vue");
const DashboardPage = () => import("@/Pages/DashboardPage.vue");
let UsersIndex = () => import("@/Pages/Users/UsersIndex.vue");
let RolesIndex = () => import("@/Pages/Users/RolesIndex.vue");
let EditProfile = () => import("@/Pages/Users/EditProfile.vue");
let AccessTokens = () => import("@/Pages/Users/AccessTokens.vue");
let PermissionsIndex = () => import("@/Pages/Users/PermissionsIndex.vue");
const ResetPassword = () => import("@/Pages/Auth/ResetPassword.vue");
const SendEmailResetPassword = () =>
    import("@/Pages/Auth/SendEmailResetPassword.vue");
// const Register = () => import("@/Pages/Auth/Register.vue");

const Error404 = () => import("@/Pages/Error404.vue");
const routes = [
    // AuthRoutes,
    {
        /* Root Routes */
        path: "/",
        component: MainLayout,
        children: [
            { path: "", component: Home, name: "Home" },
            { path: "about", component: About, name: "About" },
            { path: "forms", component: Forms, name: "Forms" },
            {
                path: "login",
                name: "login",
                component: LoginPage,
                meta: {
                    guest: true,
                },
            },
            {
                path: "send-email",
                name: "Recuperación de clave",
                component: SendEmailResetPassword,
                meta: {
                    guest: true,
                },
            },
            {
                path: "reset-password/:token",
                name: "Cambio de clave",
                component: ResetPassword,
                meta: {
                    guest: true,
                },
            },

            {
                path: "dashboard",
                component: DashboardPage,
                name: "Dashboard",
                meta: {
                    auth: true,
                },
            },
            {
                path: "users",
                component: UsersIndex,
                name: "Usuarios",
                meta: {
                    auth: true,
                },
            },
            {
                path: "permissions",
                component: PermissionsIndex,
                props: true,
                name: "Permisos",
                meta: {
                    auth: true,
                },
            },
            {
                path: "roles",
                component: RolesIndex,
                props: true,
                name: "Roles",
                meta: {
                    auth: true,
                },
            },
            {
                path: "profile",
                component: EditProfile,
                props: true,
                name: "Profile",
                meta: {
                    auth: true,
                },
            },
            {
                path: "access-tokens",
                component: AccessTokens,
                props: true,
                name: "Access Tokens",
                meta: {
                    auth: true,
                },
            },
        ],
    },
    {
        path: "/:catchAll(.*)*",
        component: Error404,
    },
];
export default routes;
