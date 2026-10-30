export default function NoteList({ notes, onDelete }) {
    return (
        <div className="space-y-2">
            {notes.map((note) => (
                <div key={note.id} className="flex items-start justify-between border p-3 dark:border-zinc-700">
                    <div>
                        <p>{note.text}</p>
                        <small className="text-gray-500">Tag: {note.tag?.name ?? '—'}</small>
                    </div>
                    <button type="button" onClick={() => onDelete(note.id)} className="text-sm text-red-500">Delete</button>
                </div>
            ))}
        </div>
    );
}
