# GraphQL API

MyTube exposes a GraphQL endpoint for archive content and user-specific actions.

## Endpoint

- Route prefix: `/graphql`
- Methods: `GET` and `POST`

Most clients should use `POST /graphql` with a JSON body containing a `query` field.

## Schemas

- `default` schema (public): `videos`, `channels`
- `user` schema (authenticated): `user`, `setFavoriteVideo`, `setFavoriteChannel`

Use a Bearer token for authenticated `user` schema operations.

## Example: list recent videos

```graphql
{
  videos(page: 1) {
    data {
      id
      uuid
      title
      published_at
      channel {
        id
        uuid
        title
        type
      }
    }
    current_page
    last_page
    per_page
    total
  }
}
```

## Example: search channels

```graphql
{
  channels(search: "Example Creator") {
    data {
      id
      uuid
      title
      type
    }
    current_page
    total
  }
}
```

## Example: filter videos by channel

```graphql
{
  videos(channel_id: 6, page: 2) {
    data {
      uuid
      title
      published_at
    }
    current_page
    total
  }
}
```

## Example request with curl

```bash
curl -X POST http://localhost/graphql \
  -H 'Content-Type: application/json' \
  -d '{"query":"{ videos(page: 1) { data { uuid title } total } }"}'
```

## Authenticated mutation example

```graphql
mutation {
  setFavoriteVideo(uuid: "PF4YxGsF1DY", favorite: true)
}
```

Send your token as:

```http
Authorization: Bearer <token>
```

## Query argument reference

`videos` supports:

- `id: Int`
- `uuid: String`
- `channel_id: Int`
- `search: String`
- `page: Int`

`channels` supports:

- `id: Int`
- `uuid: String`
- `search: String`
- `page: Int`
