export default {
  // Version Info
  MAJOR: 2,
  MINOR: 0,
  PATCH: 1,
  REVISION: 0,

  error: '',
  isLoggedIn: false,
  colors: [
    {Name: 'red', Normal: '#FF0000', Dark: '#990000', Light: '#FF6565'},
    {Name: 'yellow', Normal: '#FFFF00', Dark: '#999900', Light: '#FFFF65'},
    {Name: 'green', Normal: '#00FF00', Dark: '#009900', Light: '#65FF65'},
    {Name: 'blue', Normal: '#0000FF', Dark: '#000099', Light: '#6565FF'},
    {Name: 'cyan', Normal: '#00FFFF', Dark: '#009999', Light: '#65FFFF'},
    {Name: 'magenta', Normal: '#FF00FF', Dark: '#990099', Light: '#FF65FF'},
    {Name: 'orange', Normal: '#FF5721', Dark: '#B13E0F', Light: '#FF9912'},
    {Name: 'purple', Normal: '#9900CC', Dark: '#660099', Light: '#9933CC'},
    {Name: 'gray', Normal: '#666666', Dark: '#333333', Light: '#CCCCCC'}
  ],

  // The server
  send: (_, __) => {},

  // Populated by the server
  user: {},
  chat: [],
  stats: {},
  players: []
}
