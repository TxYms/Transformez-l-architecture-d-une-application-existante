import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router';
import Alert from '../components/Alert';
import AuthCard from '../components/AuthCard';
import Field from '../components/Field';
import { clearFeedback, register } from '../../store/authSlice';
import { selectAuthFeedback, selectAuthPending } from '../../store/selectors';

export default function RegisterPage() {
    const dispatch = useDispatch();
    const { error, errors } = useSelector(selectAuthFeedback);
    const pending = useSelector(selectAuthPending);
    const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
    const set = (field) => (value) => setForm({ ...form, [field]: value });

    useEffect(() => {
        dispatch(clearFeedback());
    }, [dispatch]);

    const submit = (event) => {
        event.preventDefault();
        dispatch(register(form));
    };

    return (
        <AuthCard title="Create an account">
            <form onSubmit={submit} className="space-y-4">
                <Alert message={error} type="error" />
                <Field label="Name" name="name" value={form.name} onChange={set('name')} errors={errors} />
                <Field label="Email address" name="email" type="email" value={form.email} onChange={set('email')} errors={errors} />
                <Field label="Password" name="password" type="password" value={form.password} onChange={set('password')} errors={errors} />
                <Field label="Confirm password" name="password_confirmation" type="password" value={form.password_confirmation} onChange={set('password_confirmation')} errors={errors} />
                <button type="submit" disabled={pending} className="w-full bg-blue-500 px-4 py-2 text-white">Create account</button>
            </form>
            <p className="text-center text-sm">Already have an account? <Link to="/login">Log in</Link></p>
        </AuthCard>
    );
}
