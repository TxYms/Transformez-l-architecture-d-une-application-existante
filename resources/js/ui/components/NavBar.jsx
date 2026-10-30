import { NavLink } from 'react-router';

export default function NavBar({ userName, onLogout }) {
    const linkClass = ({ isActive }) => (isActive ? 'font-semibold' : '');

    return (
        <nav className="flex items-center gap-6 border-b px-6 py-4 dark:border-zinc-700">
            <span className="font-bold">Renote</span>
            <NavLink to="/dashboard" className={linkClass}>Dashboard</NavLink>
            <NavLink to="/settings" className={linkClass}>Settings</NavLink>
            <span className="ml-auto text-sm">{userName}</span>
            <button type="button" onClick={onLogout} className="text-sm text-red-500">Log out</button>
        </nav>
    );
}
