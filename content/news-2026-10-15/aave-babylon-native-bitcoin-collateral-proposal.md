Babylon Labs submitted an Aave governance proposal on October 14 seeking to activate native Bitcoin as collateral on Aave V4 through its Trustless Bitcoin Vaults system.

The [Aave Request for Comment](https://governance.aave.com/t/arfc-onboard-native-btc-as-collateral-to-aave-v4-via-babylon-trustless-bitcoin-vaults/25845) describes an initial use case of borrowing stablecoins against BTC locked on the Bitcoin network. It follows an earlier temperature check that the proposal says passed.

## How the proposed integration works

The design introduces a Babylon Spoke and a BTC Vault Swap Spoke within Aave V4’s architecture. The Babylon Spoke would use native BTC as its only collateral asset.

Under the proposal, BTC remains locked in a Taproot output on Bitcoin. Records on Ethereum track the corresponding collateral and its lifecycle. An internal, transfer-restricted ERC-20 representation supports Aave’s accounting; the proposal distinguishes this from a freely transferable wrapped-Bitcoin token.

That distinction does not mean Ethereum contracts disappear from the system. Both chains and the vault’s verification and challenge mechanisms are part of the proposed arrangement.

## Governance discussion, rather than a launch notice

This is an ARFC, not confirmation that users can already borrow through the integration. The document says Aave’s risk service providers will supply the risk parameters.

Readers should watch for governance decisions, final risk settings, and a verified deployment announcement. Claims about avoiding a custodian do not, by themselves, establish that an integration has no technical or liquidation risks.

Compyvila’s October 15 edition covers the proposal filed on October 14. For related market concepts, explore [liquidity versus trading volume](/read/crypto-liquidity-vs-volume).
