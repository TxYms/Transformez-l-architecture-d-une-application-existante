export default function Alert({ message, type = 'success' }) {
    if (!message) {
        return null;
    }

    const color = type === 'error' ? 'text-red-600' : 'text-green-600';

    return <p className={`text-sm ${color}`}>{message}</p>;
}
