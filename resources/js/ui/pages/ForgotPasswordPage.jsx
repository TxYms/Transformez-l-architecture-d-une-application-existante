import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router';
import Alert from '../components/Alert';
import AuthCard from '../components/AuthCard';
import Field from '../components/Field';
import { clearFeedback, forgotPassword } from '../../store/authSlice';
import { selectAuthFeedback, selectAuthPending } from '../../store/selectors';

export default function ForgotPasswordPage() {
    const dispatch = useDispatch();
    const { message, error, errors } = useSelector(selectAuthFeedback);
    const pending = useSelector(selectAuthPending);
    const [email, setEmail] = useState('');

    useEffect(() => {
        dispatch(clearFeedback());
    }, [dispatch]);

    const submit = (event) => {
        event.preventDefault();
        dispatch(forgotPassword({ email }));
    };

    return (
        <AuthCard title="Forgot password">
            <form onSubmit={submit} className="space-y-4">
                <Alert message={message} />
                <Alert message={error} type="error" />
                <Field label="Email address" name="email" type="email" value={email} onChange={setEmail} errors={errors} />
                <button type="submit" disabled={pending} className="w-full bg-blue-500 px-4 py-2 text-white">Email password reset link</button>
            </form>
            <p className="text-center text-sm"><Link to="/login">Back to log in</Link></p>
        </AuthCard>
    );
}
