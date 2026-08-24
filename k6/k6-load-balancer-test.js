import http from 'k6/http';

export const options = {
  vus: 50,
  duration: '120s',
};

export default function () {
  http.get('http://fiap-tc-1.localtest.me:9080/api/test');
}
