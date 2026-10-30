import { createSlice, isAnyOf } from '@reduxjs/toolkit';
import { api } from '../services/api';
import { apiThunk } from './apiThunk';
import { deleteAccount, logout } from './authSlice';

export const fetchNotes = apiThunk('notes/fetch', async () => (await api.getNotes()).data, {
    condition: (_, { getState }) => getState().notes.status === 'idle',
});

export const createNote = apiThunk('notes/create', async (data) => await api.createNote(data));
export const deleteNote = apiThunk('notes/delete', async (id) => {
    await api.deleteNote(id);

    return id;
});

const initialState = {
    items: [],
    status: 'idle',
    message: null,
    error: null,
    errors: {},
};

const notesSlice = createSlice({
    name: 'notes',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchNotes.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(fetchNotes.fulfilled, (state, action) => {
                state.status = 'loaded';
                state.items = action.payload;
            })
            .addCase(fetchNotes.rejected, (state, action) => {
                state.status = 'idle';
                state.error = action.payload?.message ?? 'Server error.';
            })
            .addCase(createNote.pending, (state) => {
                state.message = null;
                state.error = null;
                state.errors = {};
            })
            .addCase(createNote.fulfilled, (state, action) => {
                state.items.unshift(action.payload.data);
                state.message = action.payload.message;
            })
            .addCase(createNote.rejected, (state, action) => {
                state.error = action.payload?.message ?? 'Server error.';
                state.errors = action.payload?.errors ?? {};
            })
            .addCase(deleteNote.fulfilled, (state, action) => {
                state.items = state.items.filter((note) => note.id !== action.payload);
            })
            .addCase(deleteNote.rejected, (state, action) => {
                state.error = action.payload?.message ?? 'Server error.';
            })
            .addMatcher(isAnyOf(logout.fulfilled, logout.rejected, deleteAccount.fulfilled), () => initialState);
    },
});

export default notesSlice.reducer;
