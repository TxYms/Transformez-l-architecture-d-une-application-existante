import { useSelector } from 'react-redux';
import { Navigate, Outlet } from 'react-router';
import { selectAuthChecked, selectUser } from '../../store/selectors';

export default function GuestShell() {
    const user = useSelector(selectUser);
    const checked = useSelector(selectAuthChecked);

    if (!checked) {
        return null;
    }

    if (user) {
        return <Navigate to="/dashboard" replace />;
    }

    return <Outlet />;
}
