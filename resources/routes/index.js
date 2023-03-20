import AuthRoutes from "./authRoutes";

// const AuthLayout = () => import("@/Layouts/AuthLayout.vue");
const About = () => import("@/Pages/About.vue");
const Home = () => import("@/Pages/HomePage.vue");
const MainLayout = () => import("@/Layouts/MainLayout.vue");
const LoginPage = () => import("@/Pages/Auth/LoginPage.vue");
const DashboardPage = () => import("@/Pages/DashboardPage.vue");
const EditProfile = () => import("@/Pages/Users/EditProfile.vue");
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
                path: "profile",
                component: EditProfile,
                props: true,
                name: "Profile",
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
