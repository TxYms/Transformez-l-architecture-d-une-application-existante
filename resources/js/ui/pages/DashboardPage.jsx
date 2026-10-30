import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import Alert from '../components/Alert';
import NoteForm from '../components/NoteForm';
import NoteList from '../components/NoteList';
import TagForm from '../components/TagForm';
import { createNote, deleteNote, fetchNotes } from '../../store/notesSlice';
import { createTag, fetchTags } from '../../store/tagsSlice';
import { selectNotes, selectNotesFeedback, selectTags, selectTagsFeedback } from '../../store/selectors';

export default function DashboardPage() {
    const dispatch = useDispatch();
    const notes = useSelector(selectNotes);
    const tags = useSelector(selectTags);
    const notesFeedback = useSelector(selectNotesFeedback);
    const tagsFeedback = useSelector(selectTagsFeedback);

    useEffect(() => {
        dispatch(fetchNotes());
        dispatch(fetchTags());
    }, [dispatch]);

    const addNote = async (data) => (await dispatch(createNote(data))).meta.requestStatus === 'fulfilled';
    const addTag = async (data) => (await dispatch(createTag(data))).meta.requestStatus === 'fulfilled';

    return (
        <div className="space-y-6">
            <section className="space-y-4 rounded-xl border p-4 dark:border-zinc-700">
                <Alert message={notesFeedback.message} />
                <Alert message={notesFeedback.error} type="error" />
                <NoteForm tags={tags} onSubmit={addNote} errors={notesFeedback.errors} />
                <h2 className="text-xl font-bold">Your Notes</h2>
                <NoteList notes={notes} onDelete={(id) => dispatch(deleteNote(id))} />
            </section>
            <section className="space-y-4 rounded-xl border p-4 dark:border-zinc-700">
                <h2 className="text-xl font-bold">Add a tag</h2>
                <Alert message={tagsFeedback.message} />
                <TagForm onSubmit={addTag} errors={tagsFeedback.errors} />
            </section>
        </div>
    );
}
