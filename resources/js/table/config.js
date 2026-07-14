const TABLE_CONTRACT_VERSION = 2;

function assertContractVersion(raw) {
  if (raw.contractVersion !== TABLE_CONTRACT_VERSION) {
    throw new Error(`Daisy Table contract mismatch: Blade contract ${raw.contractVersion ?? 'missing'}, JavaScript contract ${TABLE_CONTRACT_VERSION}. Republish the Daisy Kit assets.`);
  }
}

function freezeConfig(value) {
  if (!value || typeof value !== 'object' || Object.isFrozen(value)) {
    return value;
  }

  Object.values(value).forEach((nestedValue) => freezeConfig(nestedValue));

  return Object.freeze(value);
}

export {
  TABLE_CONTRACT_VERSION,
  assertContractVersion,
  freezeConfig,
};
