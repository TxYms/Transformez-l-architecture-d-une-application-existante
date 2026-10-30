import { createSlice, isAnyOf } from '@reduxjs/toolkit';
import { api } from '../services/api';
import { apiThunk } from './apiThunk';
import { deleteAccount, logout } from './authSlice';

export const fetchTags = apiThunk('tags/fetch', async () => (await api.getTags()).data, {
    condition: (_, { getState }) => getState().tags.status === 'idle',
});

export const createTag = apiThunk('tags/create', async (data) => await api.createTag(data));

const initialState = {
    items: [],
    status: 'idle',
    message: null,
    error: null,
    errors: {},
};

const tagsSlice = createSlice({
    name: 'tags',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchTags.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(fetchTags.fulfilled, (state, action) => {
                state.status = 'loaded';
                state.items = action.payload;
            })
            .addCase(fetchTags.rejected, (state, action) => {
                state.status = 'idle';
                state.error = action.payload?.message ?? 'Server error.';
            })
            .addCase(createTag.pending, (state) => {
                state.message = null;
                state.error = null;
                state.errors = {};
            })
            .addCase(createTag.fulfilled, (state, action) => {
                state.items.push(action.payload.data);
                state.items.sort((a, b) => a.name.localeCompare(b.name));
                state.message = action.payload.message;
            })
            .addCase(createTag.rejected, (state, action) => {
                state.error = action.payload?.message ?? 'Server error.';
                state.errors = action.payload?.errors ?? {};
            })
            .addMatcher(isAnyOf(logout.fulfilled, logout.rejected, deleteAccount.fulfilled), () => initialState);
    },
});

export default tagsSlice.reducer;
