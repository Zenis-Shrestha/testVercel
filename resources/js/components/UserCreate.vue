<template>
    <div class="container">
        <h2>Create User</h2>

        <form @submit.prevent="submitForm">
            <div>
                <label>Name</label>
                <input v-model="form.name" type="text" />
            </div>

            <div>
                <label>Age</label>
                <input v-model="form.age" type="number" />
            </div>

            <div>
                <label>Email</label>
                <input v-model="form.email" type="email" />
            </div>

            <div>
                <label>Password</label>
                <input v-model="form.password" type="password" />
            </div>

            <div>
                <label>Repeat Password</label>
                <input v-model="form.password_confirmation" type="password" />
            </div>

            <button type="submit">Save User</button>
        </form>

        <div v-if="successMessage" class="success">
            {{ successMessage }}
        </div>

        <div v-if="errors" class="error">
            <ul>
                <li v-for="(error, key) in errors" :key="key">
                    {{ error[0] }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const successMessage = ref("");
const errors = ref(null);

const form = reactive({
    name: "",
    age: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const resetForm = () => {
    form.name = "";
    form.age = "";
    form.email = "";
    form.password = "";
    form.password_confirmation = "";
};

const submitForm = async () => {
    errors.value = null;
    successMessage.value = "";

    try {
        await axios.post("/api/users", form);

        toast.success("User created yy!");
        successMessage.value = "User created successfully!";
        resetForm();
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors;
            toast.error("Validation error - check fields");
        } else {
            toast.error("Failed to create user");
        }
    }
};
</script>

<style scoped>
.container {
    width: 400px;
}

label {
    display: block;
    margin-top: 12px;
    margin-bottom: 6px;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

button {
    width: 100%;
    margin-top: 16px;
    padding: 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.error {
    color: #b91c1c;
    margin-top: 12px;
}

.success {
    color: #15803d;
    margin-top: 12px;
}
</style>
