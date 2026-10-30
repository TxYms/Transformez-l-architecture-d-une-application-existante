import { useState } from 'react';

export default function TagForm({ onSubmit, errors = {} }) {
    const [name, setName] = useState('');

    const submit = async (event) => {
        event.preventDefault();

        if (await onSubmit({ name })) {
            setName('');
        }
    };

    return (
        <form onSubmit={submit} className="space-y-2">
            <input
                type="text"
                value={name}
                onChange={(event) => setName(event.target.value)}
                placeholder="New tag name"
                className="w-full rounded border px-3 py-1 text-sm dark:bg-zinc-800"
            />
            {errors.name?.map((error) => <p key={error} className="text-xs text-red-500">{error}</p>)}
            <button type="submit" className="bg-blue-500 px-4 py-2 text-white">Add Tag</button>
        </form>
    );
}
