import { createSelector } from '@reduxjs/toolkit';

const feedback = (slice) => createSelector(
    [(state) => state[slice].message, (state) => state[slice].error, (state) => state[slice].errors],
    (message, error, errors) => ({ message, error, errors }),
);

export const selectUser = (state) => state.auth.user;
export const selectAuthChecked = (state) => state.auth.checked;
export const selectAuthPending = (state) => state.auth.pending;
export const selectAuthFeedback = feedback('auth');

export const selectNotes = (state) => state.notes.items;
export const selectNotesFeedback = feedback('notes');

export const selectTags = (state) => state.tags.items;
export const selectTagsFeedback = feedback('tags');

export const selectAppearance = (state) => state.ui.appearance;
