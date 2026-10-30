import { useDispatch, useSelector } from 'react-redux';
import { Navigate, Outlet } from 'react-router';
import NavBar from '../components/NavBar';
import { logout } from '../../store/authSlice';
import { selectAuthChecked, selectUser } from '../../store/selectors';

export default function AppShell() {
    const dispatch = useDispatch();
    const user = useSelector(selectUser);
    const checked = useSelector(selectAuthChecked);

    if (!checked) {
        return null;
    }

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    return (
        <>
            <NavBar userName={user.name} onLogout={() => dispatch(logout())} />
            <main className="mx-auto max-w-3xl p-6">
                <Outlet />
            </main>
        </>
    );
}
