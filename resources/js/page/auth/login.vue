<template>
  <div class="container relative h-screen flex-col items-center justify-center md:grid lg:max-w-none lg:grid-cols-2 lg:px-0 bg-background text-foreground">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r bg-zinc-900">
      <div class="absolute inset-0 bg-zinc-900" />
      <div class="relative z-20 flex items-center text-lg font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-6 w-6"><path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3" /></svg>
        Toyota Motor Kyushu
      </div>
      <div class="relative z-20 mt-auto">
        <blockquote class="space-y-2">
          <p class="text-lg">
            "Energy Management System for efficient and sustainable production."
          </p>
          <footer class="text-sm">EMS Team</footer>
        </blockquote>
      </div>
    </div>
    
    <div class="lg:p-8">
      <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
        <div class="flex flex-col space-y-2 text-center">
          <h1 class="text-2xl font-semibold tracking-tight">Sign in to your account</h1>
          <p class="text-sm text-muted-foreground">
            Enter your email below to access dashboard
          </p>
        </div>

        <div class="grid gap-6">
          <form @submit.prevent="login">
            <div class="grid gap-4">
              <div class="grid gap-2">
                 <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="email">Email</label>
                 <input v-model="email" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" id="email" placeholder="name@toyota.com" type="email">
              </div>
              <div class="grid gap-2">
                 <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="password">Password</label>
                 <input v-model="password" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" id="password" type="password">
              </div>
              <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
                Sign In
              </button>
            </div>
          </form>
          
          <div class="relative">
             <div class="absolute inset-0 flex items-center"><span class="w-full border-t"></span></div>
             <div class="relative flex justify-center text-xs uppercase"><span class="bg-background px-2 text-muted-foreground">Or continue with</span></div>
          </div>
          <button @click="login" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
            SSO Login
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const router = useRouter();

const login = () => {
    // Fake Login Logic
    localStorage.setItem('isAuthenticated', 'true');
    localStorage.setItem('userRole', 'admin');
    router.push('/dashboard');
};
</script>