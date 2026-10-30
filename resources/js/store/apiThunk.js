import { createAsyncThunk } from '@reduxjs/toolkit';

export function apiThunk(type, effect, options) {
    return createAsyncThunk(
        type,
        async (arg, { rejectWithValue }) => {
            try {
                return await effect(arg);
            } catch (error) {
                return rejectWithValue(error);
            }
        },
        options,
    );
}
