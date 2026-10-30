import { createSlice } from '@reduxjs/toolkit';

const uiSlice = createSlice({
    name: 'ui',
    initialState: {
        appearance: localStorage.getItem('appearance') ?? 'system',
    },
    reducers: {
        setAppearance(state, action) {
            state.appearance = action.payload;
        },
    },
});

export const { setAppearance } = uiSlice.actions;
export default uiSlice.reducer;
