function clonePublicState(state) {
  if (typeof structuredClone === 'function') {
    return structuredClone(state);
  }

  return JSON.parse(JSON.stringify(state));
}

export { clonePublicState };
