import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link, useParams, useSearchParams } from 'react-router';
import Alert from '../components/Alert';
import AuthCard from '../components/AuthCard';
import Field from '../components/Field';
import { clearFeedback, resetPassword } from '../../store/authSlice';
import { selectAuthFeedback, selectAuthPending } from '../../store/selectors';

export default function ResetPasswordPage() {
    const dispatch = useDispatch();
    const { token } = useParams();
    const [searchParams] = useSearchParams();
    const { message, error, errors } = useSelector(selectAuthFeedback);
    const pending = useSelector(selectAuthPending);
    const [form, setForm] = useState({ email: searchParams.get('email') ?? '', password: '', password_confirmation: '' });
    const set = (field) => (value) => setForm({ ...form, [field]: value });

    useEffect(() => {
        dispatch(clearFeedback());
    }, [dispatch]);

    const submit = (event) => {
        event.preventDefault();
        dispatch(resetPassword({ token, ...form }));
    };

    return (
        <AuthCard title="Reset password">
            <form onSubmit={submit} className="space-y-4">
                <Alert message={message} />
                <Alert message={error} type="error" />
                <Field label="Email" name="email" type="email" value={form.email} onChange={set('email')} errors={errors} />
                <Field label="Password" name="password" type="password" value={form.password} onChange={set('password')} errors={errors} />
                <Field label="Confirm password" name="password_confirmation" type="password" value={form.password_confirmation} onChange={set('password_confirmation')} errors={errors} />
                <button type="submit" disabled={pending} className="w-full bg-blue-500 px-4 py-2 text-white">Reset password</button>
            </form>
            <p className="text-center text-sm"><Link to="/login">Back to log in</Link></p>
        </AuthCard>
    );
}
