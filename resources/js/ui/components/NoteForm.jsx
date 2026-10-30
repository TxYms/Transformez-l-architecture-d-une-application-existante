import { useState } from 'react';

export default function NoteForm({ tags, onSubmit, errors = {} }) {
    const [text, setText] = useState('');
    const [tagId, setTagId] = useState('');

    const submit = async (event) => {
        event.preventDefault();

        if (await onSubmit({ text, tag_id: tagId ? Number(tagId) : '' })) {
            setText('');
            setTagId('');
        }
    };

    return (
        <form onSubmit={submit} className="space-y-2">
            <textarea
                value={text}
                onChange={(event) => setText(event.target.value)}
                placeholder="Write your note..."
                className="w-full border p-2 dark:bg-zinc-800"
            />
            {errors.text?.map((error) => <p key={error} className="text-xs text-red-600">{error}</p>)}
            <select
                value={tagId}
                onChange={(event) => setTagId(event.target.value)}
                className="w-full border p-2 dark:bg-zinc-800"
            >
                <option value="">-- Select Tag --</option>
                {tags.map((tag) => (
                    <option key={tag.id} value={tag.id}>{tag.name}</option>
                ))}
            </select>
            {errors.tag_id?.map((error) => <p key={error} className="text-xs text-red-600">{error}</p>)}
            <button type="submit" className="bg-blue-500 px-4 py-2 text-white">Add Note</button>
        </form>
    );
}
