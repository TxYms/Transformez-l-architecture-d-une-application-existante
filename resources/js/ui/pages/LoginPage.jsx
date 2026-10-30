import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router';
import Alert from '../components/Alert';
import AuthCard from '../components/AuthCard';
import Field from '../components/Field';
import { clearFeedback, login } from '../../store/authSlice';
import { selectAuthFeedback, selectAuthPending } from '../../store/selectors';

export default function LoginPage() {
    const dispatch = useDispatch();
    const { error, errors } = useSelector(selectAuthFeedback);
    const pending = useSelector(selectAuthPending);
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    useEffect(() => {
        dispatch(clearFeedback());
    }, [dispatch]);

    const submit = (event) => {
        event.preventDefault();
        dispatch(login({ email, password }));
    };

    return (
        <AuthCard title="Log in to your account">
            <form onSubmit={submit} className="space-y-4">
                <Alert message={error} type="error" />
                <Field label="Email address" name="email" type="email" value={email} onChange={setEmail} errors={errors} />
                <Field label="Password" name="password" type="password" value={password} onChange={setPassword} errors={errors} />
                <button type="submit" disabled={pending} className="w-full bg-blue-500 px-4 py-2 text-white">Log in</button>
            </form>
            <div className="flex justify-between text-sm">
                <Link to="/forgot-password">Forgot your password?</Link>
                <Link to="/register">Sign up</Link>
            </div>
        </AuthCard>
    );
}
