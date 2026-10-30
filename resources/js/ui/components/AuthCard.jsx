export default function AuthCard({ title, children }) {
    return (
        <div className="flex min-h-screen items-center justify-center p-6">
            <div className="w-full max-w-sm space-y-6">
                <h1 className="text-center text-2xl font-bold">{title}</h1>
                {children}
            </div>
        </div>
    );
}
