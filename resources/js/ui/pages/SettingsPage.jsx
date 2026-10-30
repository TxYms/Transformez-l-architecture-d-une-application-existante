import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import Alert from '../components/Alert';
import Field from '../components/Field';
import { clearFeedback, deleteAccount, updatePassword, updateProfile } from '../../store/authSlice';
import { setAppearance } from '../../store/uiSlice';
import { selectAppearance, selectAuthFeedback, selectAuthPending, selectUser } from '../../store/selectors';

export default function SettingsPage() {
    const dispatch = useDispatch();
    const user = useSelector(selectUser);
    const appearance = useSelector(selectAppearance);
    const { message, error, errors } = useSelector(selectAuthFeedback);
    const pending = useSelector(selectAuthPending);
    const [profile, setProfile] = useState({ name: user.name, email: user.email });
    const [passwords, setPasswords] = useState({ current_password: '', password: '', password_confirmation: '' });
    const [deletePassword, setDeletePassword] = useState('');
    const [active, setActive] = useState(null);
    const errorsFor = (form) => (active === form ? errors : {});

    useEffect(() => {
        dispatch(clearFeedback());
    }, [dispatch]);

    const submitProfile = (event) => {
        event.preventDefault();
        setActive('profile');
        dispatch(updateProfile(profile));
    };

    const submitPassword = async (event) => {
        event.preventDefault();
        setActive('password');

        if ((await dispatch(updatePassword(passwords))).meta.requestStatus === 'fulfilled') {
            setPasswords({ current_password: '', password: '', password_confirmation: '' });
        }
    };

    const submitDelete = (event) => {
        event.preventDefault();
        setActive('delete');
        dispatch(deleteAccount({ password: deletePassword }));
    };

    return (
        <div className="space-y-10">
            <Alert message={message} />
            <Alert message={error} type="error" />

            <form onSubmit={submitProfile} className="space-y-4">
                <h2 className="text-xl font-bold">Profile</h2>
                <Field label="Name" name="name" value={profile.name} onChange={(name) => setProfile({ ...profile, name })} errors={errorsFor('profile')} />
                <Field label="Email" name="email" type="email" value={profile.email} onChange={(email) => setProfile({ ...profile, email })} errors={errorsFor('profile')} />
                <button type="submit" disabled={pending} className="bg-blue-500 px-4 py-2 text-white">Save</button>
            </form>

            <form onSubmit={submitPassword} className="space-y-4">
                <h2 className="text-xl font-bold">Update password</h2>
                <Field label="Current password" name="current_password" type="password" value={passwords.current_password} onChange={(current_password) => setPasswords({ ...passwords, current_password })} errors={errorsFor('password')} />
                <Field label="New password" name="password" type="password" value={passwords.password} onChange={(password) => setPasswords({ ...passwords, password })} errors={errorsFor('password')} />
                <Field label="Confirm password" name="password_confirmation" type="password" value={passwords.password_confirmation} onChange={(password_confirmation) => setPasswords({ ...passwords, password_confirmation })} errors={errorsFor('password')} />
                <button type="submit" disabled={pending} className="bg-blue-500 px-4 py-2 text-white">Save</button>
            </form>

            <div className="space-y-4">
                <h2 className="text-xl font-bold">Appearance</h2>
                <select value={appearance} onChange={(event) => dispatch(setAppearance(event.target.value))} className="border p-2 dark:bg-zinc-800">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                    <option value="system">System</option>
                </select>
            </div>

            <form onSubmit={submitDelete} className="space-y-4">
                <h2 className="text-xl font-bold">Delete account</h2>
                <Field label="Password" name="password" id="delete_password" type="password" value={deletePassword} onChange={setDeletePassword} errors={errorsFor('delete')} />
                <button type="submit" disabled={pending} className="bg-red-600 px-4 py-2 text-white">Delete account</button>
            </form>
        </div>
    );
}
