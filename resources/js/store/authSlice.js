import { createSlice, isAnyOf, isFulfilled, isPending, isRejected } from '@reduxjs/toolkit';
import { api, getToken, setToken } from '../services/api';
import { apiThunk } from './apiThunk';

const authenticate = async (call, data) => {
    const payload = await call(data);
    setToken(payload.data.token);

    return payload.data.user;
};

export const login = apiThunk('auth/login', (data) => authenticate(api.login, data));
export const register = apiThunk('auth/register', (data) => authenticate(api.register, data));

export const fetchUser = apiThunk('auth/fetchUser', async () => {
    try {
        return (await api.getUser()).data;
    } catch (error) {
        setToken(null);
        throw error;
    }
});

export const logout = apiThunk('auth/logout', async () => {
    try {
        await api.logout();
    } finally {
        setToken(null);
    }
});

export const forgotPassword = apiThunk('auth/forgotPassword', async (data) => (await api.forgotPassword(data)).message);
export const resetPassword = apiThunk('auth/resetPassword', async (data) => (await api.resetPassword(data)).message);
export const updateProfile = apiThunk('auth/updateProfile', async (data) => (await api.updateProfile(data)).data);
export const updatePassword = apiThunk('auth/updatePassword', async (data) => (await api.updatePassword(data)).message);

export const deleteAccount = apiThunk('auth/deleteAccount', async (data) => {
    await api.deleteAccount(data);
    setToken(null);
});

const formThunks = [login, register, forgotPassword, resetPassword, updateProfile, updatePassword, deleteAccount];

const authSlice = createSlice({
    name: 'auth',
    initialState: {
        user: null,
        checked: !getToken(),
        pending: false,
        message: null,
        error: null,
        errors: {},
    },
    reducers: {
        clearFeedback(state) {
            state.message = null;
            state.error = null;
            state.errors = {};
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchUser.fulfilled, (state, action) => {
                state.user = action.payload;
                state.checked = true;
            })
            .addCase(fetchUser.rejected, (state) => {
                state.user = null;
                state.checked = true;
            })
            .addCase(deleteAccount.fulfilled, (state) => {
                state.user = null;
            })
            .addMatcher(isAnyOf(logout.fulfilled, logout.rejected), (state) => {
                state.user = null;
            })
            .addMatcher(isFulfilled(login, register, updateProfile), (state, action) => {
                state.user = action.payload;
            })
            .addMatcher(isFulfilled(forgotPassword, resetPassword, updatePassword), (state, action) => {
                state.message = action.payload;
            })
            .addMatcher(isFulfilled(updateProfile), (state) => {
                state.message = 'Profile updated.';
            })
            .addMatcher(isPending(...formThunks), (state) => {
                state.pending = true;
                state.message = null;
                state.error = null;
                state.errors = {};
            })
            .addMatcher(isFulfilled(...formThunks), (state) => {
                state.pending = false;
            })
            .addMatcher(isRejected(...formThunks), (state, action) => {
                state.pending = false;
                state.error = action.payload?.message ?? 'Server error.';
                state.errors = action.payload?.errors ?? {};
            });
    },
});

export const { clearFeedback } = authSlice.actions;
export default authSlice.reducer;
