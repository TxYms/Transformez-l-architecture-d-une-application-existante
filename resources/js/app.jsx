import { createRoot } from 'react-dom/client';
import { Provider } from 'react-redux';
import { createBrowserRouter, Navigate, RouterProvider } from 'react-router';
import { getToken, setBaseUrl } from './services/api';
import { store } from './store';
import { fetchUser } from './store/authSlice';
import AppShell from './ui/pages/AppShell';
import DashboardPage from './ui/pages/DashboardPage';
import ForgotPasswordPage from './ui/pages/ForgotPasswordPage';
import GuestShell from './ui/pages/GuestShell';
import LoginPage from './ui/pages/LoginPage';
import RegisterPage from './ui/pages/RegisterPage';
import ResetPasswordPage from './ui/pages/ResetPasswordPage';
import SettingsPage from './ui/pages/SettingsPage';

const root = document.getElementById('root');

setBaseUrl(root.dataset.apiUrl);

if (getToken()) {
    store.dispatch(fetchUser());
}

const router = createBrowserRouter([
    {
        element: <GuestShell />,
        children: [
            { path: '/login', element: <LoginPage /> },
            { path: '/register', element: <RegisterPage /> },
            { path: '/forgot-password', element: <ForgotPasswordPage /> },
            { path: '/reset-password/:token', element: <ResetPasswordPage /> },
        ],
    },
    {
        element: <AppShell />,
        children: [
            { path: '/dashboard', element: <DashboardPage /> },
            { path: '/settings', element: <SettingsPage /> },
        ],
    },
    { path: '*', element: <Navigate to="/dashboard" replace /> },
]);

createRoot(root).render(
    <Provider store={store}>
        <RouterProvider router={router} />
    </Provider>,
);
