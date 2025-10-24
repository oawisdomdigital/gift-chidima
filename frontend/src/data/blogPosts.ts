export interface BlogPost {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  featuredImage: string;
  category: string;
  tags: string[];
  author: string;
  publishDate: string;
  readTime: string;
  featured?: boolean;
}

export const blogPosts: BlogPost[] = [
  {
    id: 'leading-with-purpose',
    title: 'Leading with Purpose: The Power of Mentorship in Africa',
    excerpt: 'Discover how authentic mentorship transforms communities and builds the next generation of African leaders who will shape our continent\'s future.',
    content: `
      <p>Leadership in Africa today demands more than vision—it requires a commitment to raising others. Throughout my journey, I've witnessed firsthand how purposeful mentorship creates ripples of transformation that extend far beyond individual success stories.</p>

      <p>True mentorship begins with understanding that leadership is not about holding power, but about empowering others to discover their greatness. When we invest in emerging leaders, we're not just transferring knowledge; we're igniting purpose, building confidence, and creating pathways for others to step into their destiny.</p>

      <p>Across the continent, I've seen young leaders rise from uncertainty to excellence through the power of intentional guidance. The key is creating spaces where vulnerability is welcomed, questions are encouraged, and growth is celebrated. This is how we build sustainable leadership that transforms communities.</p>

      <p>As African leaders, we carry a responsibility to ensure our knowledge doesn't die with us. Every lesson learned, every challenge overcome, every victory celebrated—these become the inheritance we leave for those coming behind us. This is the essence of transformational leadership.</p>

      <p>The future of Africa depends on leaders who understand this principle: your greatest legacy is not what you accomplish, but who you empower to accomplish even more.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184360/pexels-photo-3184360.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Leadership',
    tags: ['Mentorship', 'Leadership', 'African Excellence', 'Transformation'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-10-10',
    readTime: '5 min read',
    featured: true
  },
  {
    id: 'faith-driven-entrepreneurship',
    title: 'Faith-Driven Entrepreneurship: Building Businesses with Kingdom Principles',
    excerpt: 'Explore how integrating faith into your business strategy creates sustainable success and meaningful impact in your community.',
    content: `
      <p>In the intersection of faith and entrepreneurship lies a powerful truth: businesses built on kingdom principles don't just survive—they transform entire communities. This is not about religious rhetoric; it's about applying timeless values of integrity, stewardship, and service to create lasting impact.</p>

      <p>When we approach business as a calling rather than just a career, everything changes. Our decisions become guided by purpose, our relationships become rooted in authenticity, and our success becomes measured not just by profit, but by the lives we touch and the legacy we build.</p>

      <p>Faith-driven entrepreneurship requires courage to operate differently in a world that often prioritizes profit over people. It means treating employees with dignity, serving customers with excellence, and using resources as a steward rather than an owner. These principles may seem countercultural, but they create businesses that stand the test of time.</p>

      <p>Throughout my journey building The New Africa Group, I've learned that when you align your business with your values, opportunities appear that money can't buy. Partnerships form based on shared vision, team members become advocates for the mission, and customers become part of a movement.</p>

      <p>The question is not whether faith belongs in business—it's whether we have the courage to let our faith shape how we do business.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Faith',
    tags: ['Faith', 'Entrepreneurship', 'Kingdom Business', 'Purpose'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-10-05',
    readTime: '6 min read'
  },
  {
    id: 'real-estate-investment-africa',
    title: 'Smart Real Estate Investment Strategies for the African Market',
    excerpt: 'Navigate the African real estate landscape with proven strategies for building wealth and creating opportunities in emerging markets.',
    content: `
      <p>Africa's real estate market represents one of the most promising investment opportunities of our generation. With rapid urbanization, growing middle class, and increasing infrastructure development, the continent is experiencing a transformation that savvy investors cannot afford to ignore.</p>

      <p>However, success in African real estate requires more than capital—it demands cultural intelligence, patience, and a commitment to creating value beyond financial returns. The most successful investors are those who understand that they're not just buying property; they're participating in community development.</p>

      <p>Key strategies include focusing on emerging cities with strong economic fundamentals, building relationships with local stakeholders, understanding regulatory environments, and thinking long-term. The quick-flip mentality that works in mature markets often fails in Africa, where sustainable success requires patience and genuine community engagement.</p>

      <p>One critical lesson I've learned is the importance of adding value through development. Whether it's residential, commercial, or mixed-use properties, investors who improve infrastructure, create jobs, and enhance communities see the greatest returns—both financial and social.</p>

      <p>The future of African real estate belongs to investors who see beyond transactions to transformation, who build not just buildings but communities, and who recognize that true wealth is created by lifting others as you rise.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/280222/pexels-photo-280222.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Real Estate',
    tags: ['Real Estate', 'Investment', 'Africa', 'Wealth Building'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-09-28',
    readTime: '7 min read'
  },
  {
    id: 'empowering-women-leaders',
    title: 'Breaking Barriers: Empowering Women Leaders in Africa',
    excerpt: 'A call to action for supporting, mentoring, and celebrating women who are reshaping Africa\'s leadership landscape.',
    content: `
      <p>The future of African leadership is female, and it's already here. Across the continent, women are breaking through barriers, shattering glass ceilings, and proving that when women lead, entire communities rise. Yet the journey is far from over.</p>

      <p>Empowering women leaders requires more than inspiration—it demands intentional action. We must create platforms for women's voices, open doors for opportunities, and build networks of support that help women navigate the unique challenges they face in leadership.</p>

      <p>One of the most powerful tools for women's empowerment is mentorship. When established leaders invest in emerging women leaders, they don't just transfer skills—they pass on confidence, open networks, and provide the encouragement needed to push through obstacles that would otherwise feel insurmountable.</p>

      <p>I've witnessed the transformation that happens when women support women. In my work across Africa, I've seen how a single connection, one word of encouragement, or a strategic introduction can change the trajectory of a woman's career and, by extension, impact entire communities.</p>

      <p>We must also challenge the systems and mindsets that limit women's advancement. This means advocating for policy changes, calling out discrimination, and refusing to accept the status quo. The barriers are real, but so is our collective power to break them.</p>

      <p>To every woman reading this: your leadership is needed, your voice matters, and your potential is limitless. And to everyone committed to Africa's transformation: empowering women isn't just the right thing to do—it's the smart strategy for building the Africa we all deserve.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Leadership',
    tags: ['Women Leadership', 'Empowerment', 'Africa', 'Mentorship'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-09-20',
    readTime: '6 min read'
  },
  {
    id: 'building-legacy-businesses',
    title: 'Building Legacy Businesses That Outlive You',
    excerpt: 'Learn the principles of creating businesses that transcend generations and create lasting impact for families and communities.',
    content: `
      <p>The true measure of entrepreneurial success is not the wealth you accumulate, but the legacy you leave. Building a business that outlives you requires shifting from a founder-centric mindset to creating systems, culture, and leadership that can thrive without you.</p>

      <p>Legacy businesses are built on three foundations: clear values, strong systems, and developed people. Values provide the compass that guides decisions across generations. Systems create the infrastructure for sustainable operations. And people—properly mentored and empowered—become the living embodiment of your vision.</p>

      <p>One critical mistake many entrepreneurs make is building businesses around their personality rather than around principles. When the business depends entirely on the founder's presence, it becomes vulnerable. True legacy requires distributing leadership, documenting processes, and creating a culture that reproduces excellence.</p>

      <p>In African contexts, legacy building must also consider family dynamics, cultural expectations, and succession planning. The most successful transitions happen when founders begin preparing successors early, create clear governance structures, and ensure the next generation understands not just how the business operates, but why it exists.</p>

      <p>Your legacy business should answer this question: What problem will this business solve for the next generation? When you build with that perspective, you create something that transcends profit to become a force for generational transformation.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184357/pexels-photo-3184357.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Entrepreneurship',
    tags: ['Legacy', 'Business', 'Succession Planning', 'Leadership'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-09-15',
    readTime: '5 min read'
  },
  {
    id: 'transforming-african-narrative',
    title: 'Transforming the African Narrative Through Media and Storytelling',
    excerpt: 'How storytelling shapes perception and why African voices must lead in telling authentic African stories.',
    content: `
      <p>For too long, the African story has been told by others. The narrative of our continent has been shaped by perspectives that emphasize challenges while overlooking triumphs, that highlight problems while ignoring solutions, that see deficit where there is abundance.</p>

      <p>This is why media and storytelling are not just communication tools—they are instruments of transformation. Through The New Africa Magazine and our various platforms, we're reclaiming the African narrative by showcasing the innovation, excellence, and leadership that define our continent.</p>

      <p>Authentic storytelling requires moving beyond stereotypes to capture the complexity and richness of African experiences. It means celebrating successes without ignoring challenges, acknowledging struggles without denying progress, and presenting Africa not as a monolith but as a diverse continent of infinite possibilities.</p>

      <p>The impact of narrative shift cannot be overstated. When young Africans see positive representations of leadership, entrepreneurship, and innovation, it expands their vision of what's possible. When global audiences encounter authentic African stories, it challenges their assumptions and opens doors for genuine partnership.</p>

      <p>Every African thought leader, entrepreneur, and creator has a responsibility to contribute to this narrative transformation. Whether through social media, traditional media, or interpersonal conversations, we must intentionally tell stories that reflect the full truth of African excellence.</p>

      <p>The Africa we speak into existence through our stories is the Africa we will build for the next generation.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Africa',
    tags: ['Media', 'Storytelling', 'African Excellence', 'Narrative'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-09-08',
    readTime: '6 min read'
  },
  {
    id: 'youth-mentorship-strategies',
    title: 'Practical Youth Mentorship Strategies That Actually Work',
    excerpt: 'Evidence-based approaches to mentoring young leaders that create measurable impact and lasting transformation.',
    content: `
      <p>Youth mentorship is one of the most powerful investments we can make in Africa's future, yet too often it falls short of its potential because we approach it casually rather than strategically. Effective mentorship requires intentionality, structure, and genuine commitment.</p>

      <p>The first principle of successful mentorship is meeting young people where they are. This means understanding their context, respecting their aspirations, and acknowledging the unique challenges their generation faces. Cookie-cutter approaches fail because every mentee's journey is unique.</p>

      <p>Practical mentorship must include three components: skill development, mindset transformation, and network access. Skills provide the tools for success. Mindset shapes how challenges are approached. Networks open doors that talent alone cannot unlock. Mentors who address all three create comprehensive transformation.</p>

      <p>One strategy that consistently works is creating structured accountability. Regular check-ins, clear goals, and measurable progress indicators help mentees stay focused and motivated. But accountability must be balanced with grace—the goal is growth, not perfection.</p>

      <p>Another critical element is exposing mentees to possibilities. Taking young leaders to conferences, introducing them to influential people, and creating opportunities for them to observe excellence in action expands their vision of what's achievable.</p>

      <p>The most effective mentors are those who view mentorship not as a favor they're doing, but as an investment in a future they want to see. When approached with this mindset, mentorship becomes transformational for both mentor and mentee.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184611/pexels-photo-3184611.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Mentorship',
    tags: ['Youth', 'Mentorship', 'Leadership Development', 'Transformation'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-08-30',
    readTime: '7 min read'
  },
  {
    id: 'navigating-leadership-challenges',
    title: 'Navigating Leadership Challenges in Uncertain Times',
    excerpt: 'Practical wisdom for leaders facing adversity, making difficult decisions, and maintaining vision during turbulent seasons.',
    content: `
      <p>Leadership is tested not in seasons of success, but in moments of uncertainty. The ability to navigate challenges, make difficult decisions, and maintain vision when circumstances are turbulent separates effective leaders from those who merely hold positions.</p>

      <p>One of the most important lessons I've learned is that uncertainty is not the enemy of leadership—it's the context in which true leadership emerges. When the path is clear and the outcome is guaranteed, anyone can lead. But when the way forward is uncertain, that's when leaders must lean on character, values, and wisdom.</p>

      <p>During challenging times, leaders must resist the temptation to react impulsively. The most effective approach is to create space for strategic thinking—to pause, assess, consult trusted advisors, and make decisions from a place of clarity rather than panic. This doesn't mean being slow; it means being thoughtful.</p>

      <p>Communication becomes even more critical during uncertainty. Teams need to know that their leader sees the challenges, has a plan, and remains committed to the vision. Transparency about difficulties, coupled with confidence about the future, creates the psychological safety that teams need to perform under pressure.</p>

      <p>Perhaps most importantly, leaders must maintain their own emotional and spiritual health. You cannot pour from an empty cup. Regular rest, maintaining supportive relationships, and staying connected to your purpose are not optional—they're essential for sustained leadership effectiveness.</p>

      <p>Remember: the challenges you face today are developing the wisdom you'll need for tomorrow. Lead through this season with courage, and you'll emerge stronger.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184296/pexels-photo-3184296.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Leadership',
    tags: ['Leadership', 'Crisis Management', 'Resilience', 'Decision Making'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-08-22',
    readTime: '6 min read'
  },
  {
    id: 'personal-branding-leaders',
    title: 'Personal Branding for Leaders: Authenticity Over Perfection',
    excerpt: 'Why authentic personal branding matters for leaders and how to build a brand that reflects your true values and vision.',
    content: `
      <p>In today's connected world, every leader has a personal brand—whether they manage it intentionally or not. The question is not whether you have a brand, but whether your brand accurately reflects who you are and what you stand for.</p>

      <p>Many leaders make the mistake of pursuing perfection in their personal branding, carefully curating an image that looks impressive but feels hollow. The truth is, people don't connect with perfection—they connect with authenticity. They want to see the real person behind the title, complete with struggles, growth, and genuine humanity.</p>

      <p>Effective personal branding begins with clarity about your core values, your unique strengths, and the impact you want to make. When these three elements align, your brand becomes a natural expression of who you are rather than a manufactured image you're trying to maintain.</p>

      <p>Consistency is crucial, but not in the sense of being one-dimensional. True consistency means that regardless of the platform or context, people encounter the same core values and authentic presence. You can be multifaceted while remaining genuine.</p>

      <p>Social media has democratized personal branding, giving every leader a platform to share their voice. The key is using these platforms strategically—sharing insights that add value, engaging in meaningful conversations, and building a community around shared vision rather than just accumulating followers.</p>

      <p>Your personal brand is your legacy in digital form. Make sure it reflects not just what you've accomplished, but who you are and what you stand for.</p>
    `,
    featuredImage: 'https://images.pexels.com/photos/3184639/pexels-photo-3184639.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category: 'Leadership',
    tags: ['Personal Branding', 'Authenticity', 'Social Media', 'Leadership'],
    author: 'Dr. Gift Chidima Nnamoko Orairu',
    publishDate: '2025-08-15',
    readTime: '5 min read'
  }
];

export const categories = ['All', 'Leadership', 'Faith', 'Real Estate', 'Africa', 'Mentorship', 'Entrepreneurship'];

export const getPostById = (id: string): BlogPost | undefined => {
  return blogPosts.find(post => post.id === id);
};

export const getRelatedPosts = (currentPostId: string, category: string, limit: number = 3): BlogPost[] => {
  return blogPosts
    .filter(post => post.id !== currentPostId && post.category === category)
    .slice(0, limit);
};

export const getFeaturedPost = (): BlogPost | undefined => {
  return blogPosts.find(post => post.featured);
};

export const getPostsByCategory = (category: string): BlogPost[] => {
  if (category === 'All') return blogPosts;
  return blogPosts.filter(post => post.category === category);
};
