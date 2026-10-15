## What it does

Walrus provides decentralized storage for blobs: data that applications can publish and retrieve. Its storage network is coordinated using resources on Sui. It should be understood as storage infrastructure, rather than assumed to be another general-purpose smart-contract blockchain. The [official network documentation](https://docs.wal.app/docs/system-overview/available-networks) distinguishes production and test environments.

## The 2025 milestone

Walrus announced production mainnet availability on **March 27, 2025**. The [mainnet announcement](https://docs.wal.app/blog/06_mainnet) says the first epoch began on March 25. This listing uses the public mainnet announcement date; the epoch start is a separate technical event.

## Network and token context

The launch documentation describes WAL as the mainnet token used in the staking system. It also describes production blob publishing, retrieval, and Walrus Sites. Testnet resources and tokens should not be confused with their mainnet counterparts.

## What to research

- Check storage duration, renewal requirements, and the terms under which your application expects data to remain available.
- Distinguish a storage guarantee from confidentiality. Assess encryption and access control separately.
- Identify dependencies such as gateways, application frontends, and Sui transactions.
- Verify that the documentation and endpoints you follow belong to the intended network.

For a practical research exercise, describe one file’s full lifecycle: upload, payment, retrieval, renewal, and the point at which availability is no longer expected. This reveals more about suitability than a token’s market price alone.

## Useful next reads

Review [seed phrases versus private keys](/read/seed-phrase-vs-private-key) for wallet-secret basics. For token valuation terminology, continue with [market cap versus FDV](/read/market-cap-vs-fdv).
