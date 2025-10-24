import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { LayoutWrapper } from './components/LayoutWrapper';
import { Home } from './pages/Home';
import { Blog } from './pages/Blog';
import { BlogPost } from './pages/BlogPost';
import { BookMe } from './pages/BookMe';
import { Store } from './pages/Store';
import { Gallery } from './pages/Gallery';

function App() {
  return (
    <BrowserRouter>
      <LayoutWrapper>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/blog" element={<Blog />} />
          <Route path="/blog/:id" element={<BlogPost />} />
          <Route path="/book-me" element={<BookMe />} />
          <Route path="/store" element={<Store />} />
          <Route path="/gallery" element={<Gallery />} />
        </Routes>
      </LayoutWrapper>
    </BrowserRouter>
  );
}

export default App;
