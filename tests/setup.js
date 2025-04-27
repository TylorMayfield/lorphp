// Configure Jest's test environment
import '@testing-library/jest-dom';

// Mock any browser APIs that might not be available in the test environment
global.MutationObserver = class {
  constructor(callback) {}
  disconnect() {}
  observe(element, initObject) {}
};

// Define any global functions or properties needed for tests
window.scrollTo = jest.fn();

// Setup any custom matchers or global test configurations here
