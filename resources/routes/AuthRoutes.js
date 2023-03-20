let LoginPage = () => import("../pages/Auth/LoginPage.vue");
let AuthLayout = () => import("../layouts/AuthLayout.vue");
let ResetPassword = () => import("../pages/Auth/ResetPassword.vue");
let SendEmailResetPassword = () =>
    import("../pages/Auth/SendEmailResetPassword.vue");

let authPages = {
    path: "/",
    redirect: "/home",
    component: AuthLayout,
    name: "Authentication",
    children: [
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
    ],
};
export default authPages;
