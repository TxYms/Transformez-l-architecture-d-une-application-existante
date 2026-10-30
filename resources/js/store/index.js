import { configureStore, createListenerMiddleware } from '@reduxjs/toolkit';
import authReducer from './authSlice';
import notesReducer from './notesSlice';
import tagsReducer from './tagsSlice';
import uiReducer, { setAppearance } from './uiSlice';

const listener = createListenerMiddleware();

function applyAppearance(appearance) {
    const dark = appearance === 'dark'
        || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    document.documentElement.classList.toggle('dark', dark);
}

listener.startListening({
    actionCreator: setAppearance,
    effect: (action) => {
        localStorage.setItem('appearance', action.payload);
        applyAppearance(action.payload);
    },
});

export const store = configureStore({
    reducer: {
        auth: authReducer,
        notes: notesReducer,
        tags: tagsReducer,
        ui: uiReducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().prepend(listener.middleware),
});

applyAppearance(store.getState().ui.appearance);
