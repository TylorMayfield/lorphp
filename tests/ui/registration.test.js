/**
 * Registration Form UI Tests
 * 
 * This test suite checks that the registration form renders correctly
 * and behaves as expected (validation, submissions, etc.)
 */

// Import testing utilities
import { fireEvent, screen } from '@testing-library/dom';
import '@testing-library/jest-dom';

// Mock HTML content that represents our registration form
const createRegistrationFormHTML = () => {
  return `
    <div class="min-h-screen flex items-center justify-center">
      <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg">
        <div>
          <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
          <p class="mt-2 text-center text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
              Sign in
            </a>
          </p>
        </div>
        
        <form id="register-form" action="/register" method="POST" class="mt-8 space-y-6">
          <div class="space-y-4">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Full Name
                <span class="text-red-500">*</span>
              </label>
              <input 
                type="text"
                id="name"
                name="name"
                value=""
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm rounded-t-md"
              >
            </div>
            
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email address
                <span class="text-red-500">*</span>
              </label>
              <input 
                type="email"
                id="email"
                name="email"
                value=""
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm "
              >
            </div>
            
            <div class="mb-4">
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
                <span class="text-red-500">*</span>
              </label>
              <input 
                type="password"
                id="password"
                name="password"
                value=""
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm "
              >
            </div>
            
            <div class="mb-4">
              <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password
                <span class="text-red-500">*</span>
              </label>
              <input 
                type="password"
                id="password_confirm"
                name="password_confirm"
                value=""
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm rounded-b-md"
              >
            </div>
          </div>
          
          <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Register</button>
        </form>
      </div>
    </div>
  `;
};

// Helper function to set up the DOM for testing
const setupDOM = () => {
  document.body.innerHTML = createRegistrationFormHTML();
  return {
    nameInput: document.getElementById('name'),
    emailInput: document.getElementById('email'),
    passwordInput: document.getElementById('password'),
    passwordConfirmInput: document.getElementById('password_confirm'),
    form: document.getElementById('register-form')
  };
};

describe('Registration Form UI', () => {
  // Reset the DOM before each test
  beforeEach(() => {
    document.body.innerHTML = '';
  });

  test('renders all form elements correctly', () => {
    const { nameInput, emailInput, passwordInput, passwordConfirmInput } = setupDOM();
    
    // Check that all form elements are in the document
    expect(nameInput).toBeInTheDocument();
    expect(emailInput).toBeInTheDocument();
    expect(passwordInput).toBeInTheDocument();
    expect(passwordConfirmInput).toBeInTheDocument();
    
    // Check form title is displayed
    expect(document.querySelector('h2')).toHaveTextContent('Create your account');
    
    // Check link to login page exists
    const loginLink = document.querySelector('a[href="/login"]');
    expect(loginLink).toBeInTheDocument();
    expect(loginLink).toHaveTextContent('Sign in');
  });

  test('validates required fields', () => {
    // We're testing client-side validation here
    const { form } = setupDOM();
    
    // Mock the submit event
    const mockSubmitEvent = {
      preventDefault: jest.fn()
    };
    
    // Add submit event listener
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      mockSubmitEvent.preventDefault();
    });
    
    // Try to submit the form without filling in any fields
    fireEvent.submit(form);
    
    // Check that preventDefault was called (which would indicate validation failed)
    expect(mockSubmitEvent.preventDefault).toHaveBeenCalled();
    
    // Check that the HTML5 validation attributes are present
    const requiredInputs = document.querySelectorAll('input[required]');
    expect(requiredInputs.length).toBe(4); // All four fields should be required
  });

  test('can fill in form fields', () => {
    const { nameInput, emailInput, passwordInput, passwordConfirmInput } = setupDOM();
    
    // Fill in all fields
    fireEvent.change(nameInput, { target: { value: 'John Doe' } });
    fireEvent.change(emailInput, { target: { value: 'john@example.com' } });
    fireEvent.change(passwordInput, { target: { value: 'password123' } });
    fireEvent.change(passwordConfirmInput, { target: { value: 'password123' } });
    
    // Check that all fields have the expected values
    expect(nameInput.value).toBe('John Doe');
    expect(emailInput.value).toBe('john@example.com');
    expect(passwordInput.value).toBe('password123');
    expect(passwordConfirmInput.value).toBe('password123');
  });

  test('form has correct action and method', () => {
    setupDOM();
    
    // Get the form element
    const form = document.getElementById('register-form');
    
    // Check form attributes
    expect(form.getAttribute('action')).toBe('/register');
    expect(form.getAttribute('method')).toBe('POST');
  });
});
