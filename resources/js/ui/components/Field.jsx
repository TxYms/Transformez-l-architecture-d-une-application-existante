export default function Field({ label, name, id = name, type = 'text', value, onChange, errors = {} }) {
    return (
        <div className="space-y-1">
            <label htmlFor={id} className="block text-sm font-medium">{label}</label>
            <input
                id={id}
                name={name}
                type={type}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className="w-full rounded border px-3 py-2 dark:bg-zinc-800"
            />
            {errors[name]?.map((error) => (
                <p key={error} className="text-xs text-red-600">{error}</p>
            ))}
        </div>
    );
}
